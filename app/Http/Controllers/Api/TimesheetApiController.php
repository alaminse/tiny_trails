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
    private function getDriver(): ?Driver
    {
        $user = Auth::user();
        if (!$user) return null;
        return Driver::where('user_id', $user->id)->first();
    }

    // ──────────────────────────────────────────────────────
    // GET /api/driver/timesheet?month=2026-03
    //
    // Present = face_verified_at date matches the shift date
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

        // ── Get rides per shift ───────────────────────────
        $allShiftIds  = $shifts->flatten()->pluck('id');
        $ridesByShift = DB::table('driver_shift_rides as dsr')
            ->join('rides as r', 'r.id', '=', 'dsr.ride_id')
            ->whereIn('dsr.driver_shift_id', $allShiftIds)
            ->where('r.driver_id', $driver->id)
            ->select('dsr.driver_shift_id', 'r.status')
            ->get()
            ->groupBy('driver_shift_id');

        // ── Timesheets (for hours_worked history) ─────────
        $timesheets = DB::table('timesheets')
            ->where('driver_id', $driver->id)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn($t) => Carbon::parse($t->date)->toDateString());

        // ── Driver's current face_verified_at ─────────────
        $faceVerifiedAt = $driver->face_verified_at
            ? Carbon::parse($driver->face_verified_at)->toDateString()
            : null;

        // ── Build daily records ───────────────────────────
        $records = $shifts->map(function ($dayShifts, $date) use (
            $driver, $ridesByShift, $timesheets, $faceVerifiedAt
        ) {
            $ts = $timesheets->get($date);

            // ── Present check: face verified on this date ─
            $faceVerifiedOnDate = false;

            // Check timesheets shift_start (set when driver starts)
            if ($ts && !empty($ts->shift_start)) {
                $faceVerifiedOnDate = true;
            }
            // Check current face_verified_at date
            elseif ($faceVerifiedAt === $date) {
                $faceVerifiedOnDate = true;
            }

            // ── Shifts detail ─────────────────────────────
            $dayShiftData = $dayShifts->map(function ($shift) use ($ridesByShift) {
                $rides     = $ridesByShift->get($shift->id, collect());
                $total     = $rides->count();
                $completed = $rides->where('status', 'completed')->count();
                $cancelled = $rides->where('status', 'cancelled')->count();

                return [
                    'shift_id'        => $shift->id,
                    'shift_label'     => $shift->shift_label,
                    'shift_number'    => $shift->shift_number,
                    'start_time'      => $shift->start_time,
                    'end_time'        => $shift->end_time,
                    'status'          => $shift->status,
                    'total_rides'     => $total,
                    'completed_rides' => $completed,
                    'cancelled_rides' => $cancelled,
                ];
            })->values()->toArray();

            $totalRides     = collect($dayShiftData)->sum('total_rides');
            $completedRides = collect($dayShiftData)->sum('completed_rides');

            // Hours from timesheets OR calculate from shifts
            $hoursWorked = 0;
            if ($ts && $ts->hours_worked > 0) {
                $hoursWorked = (float) $ts->hours_worked;
            } else {
                foreach ($dayShiftData as $s) {
                    try {
                        $ss = Carbon::parse('2000-01-01 ' . $s['start_time']);
                        $se = Carbon::parse('2000-01-01 ' . $s['end_time']);
                        if ($se->lessThan($ss)) $se->addDay();
                        $hoursWorked += $ss->diffInMinutes($se) / 60;
                    } catch (\Exception $e) {}
                }
            }

            return [
                'date'              => $date,
                'day_name'          => Carbon::parse($date)->format('D'),
                'day_number'        => (int) Carbon::parse($date)->format('j'),
                // Present = face verified on this date
                'attendance_status' => $faceVerifiedOnDate ? 'present' : 'absent',
                'face_verified'     => $faceVerifiedOnDate,
                'hours_worked'      => round($hoursWorked, 2),
                'timesheet_status'  => $ts?->status ?? 'pending',
                'shifts'            => $dayShiftData,
                'total_rides'       => $totalRides,
                'completed_rides'   => $completedRides,
            ];
        })->values();

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
    // Last 6 months
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

            // Total shifts assigned
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

            // Hours from timesheets
            $hoursWorked = (float) DB::table('timesheets')
                ->where('driver_id', $driver->id)
                ->whereBetween('date', [$start, $end])
                ->sum('hours_worked');

            // Present days = distinct dates where face was verified
            // Using timesheets shift_start as proxy
            $presentDays = DB::table('timesheets')
                ->where('driver_id', $driver->id)
                ->whereBetween('date', [$start, $end])
                ->whereNotNull('shift_start')
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
