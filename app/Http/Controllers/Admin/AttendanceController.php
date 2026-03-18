<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\UserRolePermission\app\Models\Driver;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // ──────────────────────────────────────────────────────
    // Attendance = Present যদি সেই দিন face_verified_at set থাকে
    // drivers.face_verified_at → date check
    // ──────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $month    = $request->input('month', Carbon::now()->format('Y-m'));
        $start    = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
        $end      = Carbon::parse($month . '-01')->endOfMonth()->toDateString();
        $driverId = $request->input('driver_id');
        $view     = $request->input('view', 'date');

        $drivers = Driver::with('user')->whereNull('deleted_at')->get();

        // ── All shifts in month ───────────────────────────
        $shiftsQuery = DB::table('driver_shifts as ds')
            ->join('shift_drivers as sd', 'sd.driver_shift_id', '=', 'ds.id')
            ->join('drivers as d', 'd.id', '=', 'sd.driver_id')
            ->join('users as u', 'u.id', '=', 'd.user_id')
            ->whereBetween('ds.date', [$start, $end])
            ->whereNull('ds.deleted_at')
            ->select(
                'ds.id as shift_id',
                'ds.date',
                'ds.shift_label',
                'ds.shift_number',
                'ds.start_time',
                'ds.end_time',
                'ds.status as shift_status',
                'sd.driver_id',
                'd.face_verified_at',
                'u.first_name',
                'u.last_name',
                'u.phone'
            )
            ->orderBy('ds.date')
            ->orderBy('u.first_name');

        if ($driverId) $shiftsQuery->where('sd.driver_id', $driverId);

        $allShifts = $shiftsQuery->get();

        // ── Rides per shift ───────────────────────────────
        $shiftIds     = $allShifts->pluck('shift_id')->unique();
        $ridesByShift = DB::table('driver_shift_rides as dsr')
            ->join('rides as r', 'r.id', '=', 'dsr.ride_id')
            ->whereIn('dsr.driver_shift_id', $shiftIds)
            ->select('dsr.driver_shift_id', 'r.driver_id', 'r.status')
            ->get()
            ->groupBy('driver_shift_id');

        // ── Face verification log per driver per date ─────
        // Check drivers.face_verified_at for each driver+date
        // Also check timesheets for historical face_verified_at
        $faceVerifyLog = DB::table('timesheets')
            ->whereIn('driver_id', $allShifts->pluck('driver_id')->unique())
            ->whereBetween('date', [$start, $end])
            ->select('driver_id', 'date', 'shift_start')
            ->get()
            ->groupBy('driver_id');

        // ── Build attendance records ──────────────────────
        $attendanceRecords = $allShifts
            ->groupBy(fn($s) => $s->date . '_' . $s->driver_id)
            ->map(function ($dayShifts) use ($ridesByShift, $faceVerifyLog) {
                $first      = $dayShifts->first();
                $driverId   = $first->driver_id;
                $date       = Carbon::parse($first->date)->toDateString();

                // ── Present check: face_verified_at on this date ──
                // Check timesheets table for this driver on this date
                $tsForDay = collect($faceVerifyLog->get($driverId, collect()))
                    ->first(fn($t) => Carbon::parse($t->date)->toDateString() === $date);

                // Also check current driver face_verified_at
                $driverFaceVerifiedAt = $first->face_verified_at;
                $faceVerifiedOnDate   = false;

                if ($tsForDay && !empty($tsForDay->shift_start)) {
                    $faceVerifiedOnDate = true;
                } elseif ($driverFaceVerifiedAt) {
                    $faceVerifiedOnDate =
                        Carbon::parse($driverFaceVerifiedAt)->toDateString() === $date;
                }

                $attendanceStatus = $faceVerifiedOnDate ? 'present' : 'absent';

                // ── Shifts data ───────────────────────────
                $shiftsData = $dayShifts->map(function ($shift) use ($ridesByShift, $driverId) {
                    $rides     = $ridesByShift->get($shift->shift_id, collect())
                                     ->where('driver_id', $driverId);
                    $total     = $rides->count();
                    $completed = $rides->where('status', 'completed')->count();
                    $cancelled = $rides->where('status', 'cancelled')->count();

                    return [
                        'shift_id'        => $shift->shift_id,
                        'shift_label'     => $shift->shift_label,
                        'start_time'      => $shift->start_time,
                        'end_time'        => $shift->end_time,
                        'shift_status'    => $shift->shift_status,
                        'total_rides'     => $total,
                        'completed_rides' => $completed,
                        'cancelled_rides' => $cancelled,
                    ];
                })->values()->toArray();

                $totalRides     = collect($shiftsData)->sum('total_rides');
                $completedRides = collect($shiftsData)->sum('completed_rides');

                // Hours worked from shifts
                $hoursWorked = $dayShifts->sum(function ($shift) {
                    try {
                        $s = Carbon::parse('2000-01-01 ' . $shift->start_time);
                        $e = Carbon::parse('2000-01-01 ' . $shift->end_time);
                        if ($e->lessThan($s)) $e->addDay();
                        return $s->diffInMinutes($e) / 60;
                    } catch (\Exception $ex) { return 0; }
                });

                return [
                    'date'              => $date,
                    'day_name'          => Carbon::parse($date)->format('D'),
                    'driver_id'         => $driverId,
                    'driver_name'       => $first->first_name . ' ' . $first->last_name,
                    'driver_phone'      => $first->phone,
                    'driver_avatar'     => strtoupper(substr($first->first_name, 0, 1))
                                        . strtoupper(substr($first->last_name ?? '', 0, 1)),
                    'attendance_status' => $attendanceStatus,
                    'face_verified_at'  => $driverFaceVerifiedAt,
                    'hours_worked'      => round($hoursWorked, 2),
                    'total_rides'       => $totalRides,
                    'completed_rides'   => $completedRides,
                    'shifts'            => $shiftsData,
                ];
            })->values();

        // ── Stats ─────────────────────────────────────────
        $today        = Carbon::today()->toDateString();
        $todayRecords = $attendanceRecords->where('date', $today);

        $stats = [
            'total_drivers'     => $drivers->count(),
            'present_today'     => $todayRecords->where('attendance_status', 'present')->count(),
            'absent_today'      => $todayRecords->where('attendance_status', 'absent')->count(),
            'total_rides_month' => $attendanceRecords->sum('completed_rides'),
        ];

        $byDate   = $attendanceRecords->groupBy('date')->sortKeys();
        $byDriver = $attendanceRecords->groupBy('driver_id');

        // ── Grid data ─────────────────────────────────────
        $gridDates = collect();
        $cursor    = Carbon::parse($start);
        while ($cursor->lte(Carbon::parse($end))) {
            $gridDates->push($cursor->toDateString());
            $cursor->addDay();
        }

        $gridData = $drivers->map(function ($driver) use ($attendanceRecords, $gridDates) {
            $driverRecords = $attendanceRecords
                ->where('driver_id', $driver->id)
                ->keyBy('date');
            return [
                'driver_id'   => $driver->id,
                'driver_name' => ($driver->user?->first_name ?? '') . ' ' . ($driver->user?->last_name ?? ''),
                'days'        => $gridDates->map(fn($date) => [
                    'date'   => $date,
                    'status' => $driverRecords->get($date)['attendance_status'] ?? 'no_shift',
                    'rides'  => $driverRecords->get($date)['completed_rides'] ?? 0,
                ]),
            ];
        });

        return view('backend.attendance.index', compact(
            'attendanceRecords', 'byDate', 'byDriver', 'gridData',
            'gridDates', 'drivers', 'stats', 'month', 'view', 'driverId'
        ));
    }

    // ──────────────────────────────────────────────────────
    // GET /admin/attendance/driver/{id}
    // ──────────────────────────────────────────────────────
    public function driverDetail(Request $request, $driverId)
    {
        $month  = $request->input('month', Carbon::now()->format('Y-m'));
        $start  = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
        $end    = Carbon::parse($month . '-01')->endOfMonth()->toDateString();

        $driver = Driver::with('user')->findOrFail($driverId);

        $shifts = DB::table('driver_shifts as ds')
            ->join('shift_drivers as sd', 'sd.driver_shift_id', '=', 'ds.id')
            ->where('sd.driver_id', $driverId)
            ->whereBetween('ds.date', [$start, $end])
            ->whereNull('ds.deleted_at')
            ->orderBy('ds.date')
            ->orderBy('ds.shift_number')
            ->select('ds.*')
            ->get();

        $shiftIds     = $shifts->pluck('id');
        $ridesByShift = DB::table('driver_shift_rides as dsr')
            ->join('rides as r', 'r.id', '=', 'dsr.ride_id')
            ->whereIn('dsr.driver_shift_id', $shiftIds)
            ->where('r.driver_id', $driverId)
            ->select('dsr.driver_shift_id', 'r.id as ride_id', 'r.status', 'r.pickup', 'r.ride_type')
            ->get()
            ->groupBy('driver_shift_id');

        // Face verification history from timesheets
        $timesheets = DB::table('timesheets')
            ->where('driver_id', $driverId)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn($t) => Carbon::parse($t->date)->toDateString());

        // Current driver face_verified_at
        $driverFaceVerifiedAt = $driver->face_verified_at;

        $dailyData = $shifts
            ->groupBy(fn($s) => Carbon::parse($s->date)->toDateString())
            ->map(function ($dayShifts, $date) use ($ridesByShift, $driverFaceVerifiedAt, $timesheets) {

                // Present = face verified on this date
                $ts = $timesheets->get($date);
                $faceVerifiedOnDate = false;

                if ($ts && !empty($ts->shift_start)) {
                    $faceVerifiedOnDate = true;
                } elseif ($driverFaceVerifiedAt) {
                    $faceVerifiedOnDate =
                        Carbon::parse($driverFaceVerifiedAt)->toDateString() === $date;
                }

                $shiftsData = $dayShifts->map(function ($shift) use ($ridesByShift) {
                    $rides    = $ridesByShift->get($shift->id, collect());
                    return [
                        'shift_label'     => $shift->shift_label,
                        'start_time'      => $shift->start_time,
                        'end_time'        => $shift->end_time,
                        'total_rides'     => $rides->count(),
                        'completed_rides' => $rides->where('status', 'completed')->count(),
                        'rides'           => $rides->values(),
                    ];
                })->values();

                $completed = $shiftsData->sum('completed_rides');
                $total     = $shiftsData->sum('total_rides');

                return [
                    'date'              => $date,
                    'day_name'          => Carbon::parse($date)->format('D, d M'),
                    'attendance_status' => $faceVerifiedOnDate ? 'present' : 'absent',
                    'face_verified'     => $faceVerifiedOnDate,
                    'total_rides'       => $total,
                    'completed_rides'   => $completed,
                    'shifts'            => $shiftsData,
                ];
            })->values();

        $summary = [
            'present_days'    => $dailyData->where('attendance_status', 'present')->count(),
            'absent_days'     => $dailyData->where('attendance_status', 'absent')->count(),
            'total_rides'     => $dailyData->sum('total_rides'),
            'completed_rides' => $dailyData->sum('completed_rides'),
        ];

        return view('backend.attendance.driver_detail',
            compact('driver', 'dailyData', 'summary', 'month'));
    }

    // ──────────────────────────────────────────────────────
    // GET /admin/attendance/export
    // ──────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $start = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
        $end   = Carbon::parse($month . '-01')->endOfMonth()->toDateString();

        // Re-use index logic but export as CSV
        $records = DB::table('driver_shifts as ds')
            ->join('shift_drivers as sd', 'sd.driver_shift_id', '=', 'ds.id')
            ->join('drivers as d', 'd.id', '=', 'sd.driver_id')
            ->join('users as u', 'u.id', '=', 'd.user_id')
            ->whereBetween('ds.date', [$start, $end])
            ->whereNull('ds.deleted_at')
            ->select(
                'u.first_name', 'u.last_name', 'u.phone',
                'ds.date', 'ds.shift_label', 'ds.start_time', 'ds.end_time',
                'd.face_verified_at'
            )
            ->orderBy('ds.date')
            ->orderBy('u.first_name')
            ->get();

        $filename = "attendance_{$month}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Driver Name', 'Phone', 'Date', 'Shift',
                'Start Time', 'End Time', 'Attendance',
            ]);
            foreach ($records as $r) {
                $faceVerified = $r->face_verified_at
                    && Carbon::parse($r->face_verified_at)->toDateString() === $r->date;
                fputcsv($handle, [
                    $r->first_name . ' ' . $r->last_name,
                    $r->phone,
                    $r->date,
                    $r->shift_label,
                    $r->start_time,
                    $r->end_time,
                    $faceVerified ? 'Present' : 'Absent',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
