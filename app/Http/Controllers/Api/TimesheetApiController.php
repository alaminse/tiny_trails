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
    // ── Helper ────────────────────────────────────────────
    private function getDriver(): ?Driver
    {
        $user = Auth::user();
        if (!$user) return null;
        return Driver::where('user_id', $user->id)->first();
    }

    // ──────────────────────────────────────────────────────
    // GET /api/driver/timesheet?month=2026-03
    // timesheets table exact columns:
    // id, driver_id, ride_id, date, shift_start, shift_end,
    // hours_worked, status, approved_by, notes
    // ──────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $driver = $this->getDriver();
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
            ], 404);
        }

        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $start = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
        $end   = Carbon::parse($month . '-01')->endOfMonth()->toDateString();

        // ── Get timesheets for this driver this month ─────
        $timesheets = DB::table('timesheets')
            ->where('driver_id', $driver->id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        // ── Get shifts for this driver this month ─────────
        $shifts = DB::table('driver_shifts as ds')
            ->join('shift_drivers as sd', 'sd.driver_shift_id', '=', 'ds.id')
            ->where('sd.driver_id', $driver->id)
            ->whereBetween('ds.date', [$start, $end])
            ->whereNull('ds.deleted_at')
            ->select(
                'ds.id',
                'ds.date',
                'ds.shift_label',
                'ds.shift_number',
                'ds.start_time',
                'ds.end_time',
                'ds.status'
            )
            ->orderBy('ds.date')
            ->orderBy('ds.shift_number')
            ->get()
            ->groupBy(fn($s) => Carbon::parse($s->date)->toDateString());

        // ── Build daily records ───────────────────────────
        $allDates = $shifts->keys()->merge(
            $timesheets->pluck('date')->map(fn($d) => Carbon::parse($d)->toDateString())
        )->unique()->sort()->values();

        $records = $allDates->map(function ($date) use ($driver, $timesheets, $shifts) {

            // Timesheet row for this date (may not exist)
            $ts = $timesheets->first(fn($t) =>
                Carbon::parse($t->date)->toDateString() === $date
            );

            // Shifts for this date
            $dayShifts = $shifts->get($date, collect());

            // Rides per shift
            $dayShiftData = $dayShifts->map(function ($shift) use ($driver) {
                $rides = DB::table('driver_shift_rides as dsr')
                    ->join('rides as r', 'r.id', '=', 'dsr.ride_id')
                    ->where('dsr.driver_shift_id', $shift->id)
                    ->where('r.driver_id', $driver->id)
                    ->pluck('r.status');

                return [
                    'shift_id'        => $shift->id,
                    'shift_label'     => $shift->shift_label,
                    'shift_number'    => $shift->shift_number,
                    'start_time'      => $shift->start_time,
                    'end_time'        => $shift->end_time,
                    'status'          => $shift->status,
                    'total_rides'     => $rides->count(),
                    'completed_rides' => $rides->filter(fn($s) => $s === 'completed')->count(),
                    'cancelled_rides' => $rides->filter(fn($s) => $s === 'cancelled')->count(),
                ];
            })->values()->toArray();

            $totalRides     = collect($dayShiftData)->sum('total_rides');
            $completedRides = collect($dayShiftData)->sum('completed_rides');

            // Hours worked — from timesheet row OR calculate from shifts
            $hoursWorked = 0;
            if ($ts && $ts->hours_worked > 0) {
                $hoursWorked = (float) $ts->hours_worked;
            } else {
                foreach ($dayShiftData as $s) {
                    try {
                        $shiftStart = Carbon::parse('2000-01-01 ' . $s['start_time']);
                        $shiftEnd   = Carbon::parse('2000-01-01 ' . $s['end_time']);
                        if ($shiftEnd->lessThan($shiftStart)) $shiftEnd->addDay();
                        $hoursWorked += $shiftStart->diffInMinutes($shiftEnd) / 60;
                    } catch (\Exception $e) {}
                }
            }

            // Attendance — present if any completed ride or hours > 0
            $attendanceStatus = ($completedRides > 0 || $hoursWorked > 0)
                ? 'present'
                : (count($dayShiftData) > 0 ? 'absent' : 'no_shift');

            return [
                'date'              => $date,
                'day_name'          => Carbon::parse($date)->format('D'),
                'day_number'        => Carbon::parse($date)->format('j'),
                'attendance_status' => $attendanceStatus,
                'hours_worked'      => round($hoursWorked, 2),
                'timesheet_status'  => $ts?->status ?? 'pending',
                'approved_by'       => $ts?->approved_by,
                'shift_start'       => $ts?->shift_start,
                'shift_end'         => $ts?->shift_end,
                'notes'             => $ts?->notes,
                'shifts'            => $dayShiftData,
                'total_rides'       => $totalRides,
                'completed_rides'   => $completedRides,
            ];
        })->filter(fn($r) => $r['attendance_status'] !== 'no_shift')
          ->values();

        // ── Summary ───────────────────────────────────────
        $presentDays    = $records->where('attendance_status', 'present')->count();
        $absentDays     = $records->where('attendance_status', 'absent')->count();
        $totalDays      = $records->count();
        $totalHours     = round($records->sum('hours_worked'), 2);
        $totalRides     = $records->sum('total_rides');
        $completedRides = $records->sum('completed_rides');

        return response()->json([
            'success' => true,
            'data'    => [
                'month'   => $month,
                'summary' => [
                    'total_days'       => $totalDays,
                    'present_days'     => $presentDays,
                    'absent_days'      => $absentDays,
                    'total_hours'      => $totalHours,
                    'total_rides'      => $totalRides,
                    'completed_rides'  => $completedRides,
                    'attendance_rate'  => $totalDays > 0
                        ? round(($presentDays / $totalDays) * 100, 1)
                        : 0,
                ],
                'records' => $records,
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────
    // GET /api/driver/timesheet/summary
    // Last 6 months summary
    // ──────────────────────────────────────────────────────
    public function summary(Request $request)
    {
        $driver = $this->getDriver();
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
            ], 404);
        }

        $months = collect();

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $start = $month->copy()->startOfMonth()->toDateString();
            $end   = $month->copy()->endOfMonth()->toDateString();

            // Total shifts
            $totalShifts = DB::table('driver_shifts as ds')
                ->join('shift_drivers as sd', 'sd.driver_shift_id', '=', 'ds.id')
                ->where('sd.driver_id', $driver->id)
                ->whereBetween('ds.date', [$start, $end])
                ->whereNull('ds.deleted_at')
                ->count();

            // Completed rides
            $completedRides = DB::table('driver_shift_rides as dsr')
                ->join('rides as r', 'r.id', '=', 'dsr.ride_id')
                ->join('driver_shifts as ds', 'ds.id', '=', 'dsr.driver_shift_id')
                ->join('shift_drivers as sd', 'sd.driver_shift_id', '=', 'ds.id')
                ->where('sd.driver_id', $driver->id)
                ->where('r.driver_id', $driver->id)
                ->where('r.status', 'completed')
                ->whereBetween('ds.date', [$start, $end])
                ->count();

            // Hours from timesheets table
            $hoursWorked = (float) DB::table('timesheets')
                ->where('driver_id', $driver->id)
                ->whereBetween('date', [$start, $end])
                ->sum('hours_worked');

            // Present days (from timesheets)
            $presentDays = DB::table('timesheets')
                ->where('driver_id', $driver->id)
                ->whereBetween('date', [$start, $end])
                ->where(fn($q) => $q->where('hours_worked', '>', 0)
                    ->orWhereNotNull('shift_start'))
                ->count();

            $months->push([
                'month'           => $month->format('Y-m'),
                'month_label'     => $month->format('M Y'),
                'total_shifts'    => $totalShifts,
                'completed_rides' => $completedRides,
                'hours_worked'    => round($hoursWorked, 2),
                'present_days'    => $presentDays,
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $months,
        ]);
    }
}
