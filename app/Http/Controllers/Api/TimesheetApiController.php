<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\UserRolePermission\app\Models\Driver;
use Carbon\Carbon;

class TimesheetApiController extends Controller
{
    // ──────────────────────────────────────────────────────
    // GET /admin/timesheet
    // ──────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $month    = $request->input('month', Carbon::now()->format('Y-m'));
        $start    = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
        $end      = Carbon::parse($month . '-01')->endOfMonth()->toDateString();
        $driverId = $request->input('driver_id');
        $status   = $request->input('status');

        // ── Query timesheets table (exact columns only) ───
        $query = DB::table('timesheets as t')
            ->join('drivers as d', 'd.id', '=', 't.driver_id')
            ->join('users as u', 'u.id', '=', 'd.user_id')
            ->select(
                't.id',
                't.driver_id',
                't.ride_id',
                't.date',
                't.shift_start',
                't.shift_end',
                't.hours_worked',
                't.status',
                't.approved_by',
                't.notes',
                't.created_at',
                't.updated_at',
                'u.first_name',
                'u.last_name',
                'u.phone',
                'd.id as driver_table_id'
            )
            ->whereBetween('t.date', [$start, $end])
            ->orderBy('t.date', 'desc')
            ->orderBy('u.first_name');

        if ($driverId) $query->where('t.driver_id', $driverId);
        if ($status)   $query->where('t.status', $status);

        $timesheets = $query->paginate(25);

        // ── Enrich each row with shift + ride data ────────
        $timesheets->getCollection()->transform(function ($ts) {

            // Shifts for this driver on this date
            $shifts = DB::table('driver_shifts as ds')
                ->join('shift_drivers as sd', 'sd.driver_shift_id', '=', 'ds.id')
                ->where('sd.driver_id', $ts->driver_id)
                ->whereDate('ds.date', $ts->date)
                ->whereNull('ds.deleted_at')
                ->select(
                    'ds.id',
                    'ds.shift_label',
                    'ds.shift_number',
                    'ds.start_time',
                    'ds.end_time',
                    'ds.status'
                )
                ->get();

            // Rides per shift
            $ts->shifts = $shifts->map(function ($shift) use ($ts) {
                $rides = DB::table('driver_shift_rides as dsr')
                    ->join('rides as r', 'r.id', '=', 'dsr.ride_id')
                    ->where('dsr.driver_shift_id', $shift->id)
                    ->where('r.driver_id', $ts->driver_id)
                    ->pluck('r.status');

                return [
                    'shift_label'     => $shift->shift_label,
                    'start_time'      => $shift->start_time,
                    'end_time'        => $shift->end_time,
                    'status'          => $shift->status,
                    'total_rides'     => $rides->count(),
                    'completed_rides' => $rides->filter(fn($s) => $s === 'completed')->count(),
                    'cancelled_rides' => $rides->filter(fn($s) => $s === 'cancelled')->count(),
                ];
            })->toArray();

            // Totals
            $ts->total_rides     = collect($ts->shifts)->sum('total_rides');
            $ts->completed_rides = collect($ts->shifts)->sum('completed_rides');

            // Attendance: present if any completed ride OR hours_worked > 0
            $ts->attendance_status = ($ts->completed_rides > 0 || $ts->hours_worked > 0)
                ? 'present'
                : 'absent';

            // Approved by name
            $ts->approved_by_name = $ts->approved_by
                ? DB::table('users')->where('id', $ts->approved_by)->value('first_name')
                : null;

            return $ts;
        });

        // ── Stats ─────────────────────────────────────────
        $todayPresent = DB::table('timesheets')
            ->whereDate('date', Carbon::today())
            ->where(function ($q) {
                $q->where('hours_worked', '>', 0)
                  ->orWhereNotNull('shift_start');
            })
            ->distinct('driver_id')
            ->count('driver_id');

        $stats = [
            'total_drivers'    => Driver::whereNull('deleted_at')->count(),
            'total_present'    => $todayPresent,
            'pending_approval' => DB::table('timesheets')
                ->whereBetween('date', [$start, $end])
                ->where('status', 'pending')
                ->count(),
            'total_hours'      => round(
                DB::table('timesheets')
                    ->whereBetween('date', [$start, $end])
                    ->sum('hours_worked'),
                1
            ),
        ];

        $drivers = Driver::with('user')->whereNull('deleted_at')->get();

        return view('backend.timesheet.index', compact(
            'timesheets', 'stats', 'drivers', 'month'
        ));
    }

    // ──────────────────────────────────────────────────────
    // GET /admin/timesheet/{id}/detail  (AJAX)
    // ──────────────────────────────────────────────────────
    public function detail($id)
    {
        $timesheet = DB::table('timesheets as t')
            ->join('drivers as d', 'd.id', '=', 't.driver_id')
            ->join('users as u', 'u.id', '=', 'd.user_id')
            ->where('t.id', $id)
            ->select(
                't.*',
                'u.first_name', 'u.last_name', 'u.phone'
            )
            ->first();

        if (!$timesheet) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $shifts = DB::table('driver_shifts as ds')
            ->join('shift_drivers as sd', 'sd.driver_shift_id', '=', 'ds.id')
            ->where('sd.driver_id', $timesheet->driver_id)
            ->whereDate('ds.date', $timesheet->date)
            ->whereNull('ds.deleted_at')
            ->get()
            ->map(function ($shift) use ($timesheet) {
                $rides = DB::table('driver_shift_rides as dsr')
                    ->join('rides as r', 'r.id', '=', 'dsr.ride_id')
                    ->where('dsr.driver_shift_id', $shift->id)
                    ->where('r.driver_id', $timesheet->driver_id)
                    ->pluck('r.status');

                return [
                    'shift_label'     => $shift->shift_label,
                    'start_time'      => $shift->start_time,
                    'end_time'        => $shift->end_time,
                    'status'          => $shift->status,
                    'total_rides'     => $rides->count(),
                    'completed_rides' => $rides->filter(fn($s) => $s === 'completed')->count(),
                ];
            });

        return view('backend.timesheet.partials.detail',
            compact('timesheet', 'shifts'));
    }

    // ──────────────────────────────────────────────────────
    // PATCH /admin/timesheet/{id}/status
    // ──────────────────────────────────────────────────────
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $updated = DB::table('timesheets')->where('id', $id)->update([
            'status'      => $request->status,
            'approved_by' => Auth::id(),
            'updated_at'  => Carbon::now(),
        ]);

        return response()->json([
            'success' => $updated > 0,
            'message' => 'Status updated to ' . $request->status,
        ]);
    }

    // ──────────────────────────────────────────────────────
    // POST /admin/timesheet/approve-all
    // ──────────────────────────────────────────────────────
    public function approveAll(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $start = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
        $end   = Carbon::parse($month . '-01')->endOfMonth()->toDateString();

        $count = DB::table('timesheets')
            ->whereBetween('date', [$start, $end])
            ->where('status', 'pending')
            ->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
                'updated_at'  => Carbon::now(),
            ]);

        return response()->json([
            'success' => true,
            'count'   => $count,
            'message' => "{$count} timesheets approved.",
        ]);
    }

    // ──────────────────────────────────────────────────────
    // GET /admin/timesheet/export
    // ──────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $start = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
        $end   = Carbon::parse($month . '-01')->endOfMonth()->toDateString();

        $records = DB::table('timesheets as t')
            ->join('drivers as d', 'd.id', '=', 't.driver_id')
            ->join('users as u', 'u.id', '=', 'd.user_id')
            ->whereBetween('t.date', [$start, $end])
            ->select(
                'u.first_name', 'u.last_name', 'u.phone',
                't.date', 't.shift_start', 't.shift_end',
                't.hours_worked', 't.status', 't.notes'
            )
            ->orderBy('t.date')
            ->orderBy('u.first_name')
            ->get();

        $filename = "timesheets_{$month}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Driver Name', 'Phone', 'Date',
                'Shift Start', 'Shift End',
                'Hours Worked', 'Status', 'Notes',
            ]);
            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->first_name . ' ' . $r->last_name,
                    $r->phone,
                    $r->date,
                    $r->shift_start,
                    $r->shift_end,
                    $r->hours_worked,
                    $r->status,
                    $r->notes,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
