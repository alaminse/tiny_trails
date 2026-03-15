<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DriverShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\UserRolePermission\app\Models\Driver;
use Carbon\Carbon;

class DriverShiftApiController extends Controller
{
    // ── Helper ────────────────────────────────────────────
    private function getDriver(): ?Driver
    {
        $user = Auth::user();
        if (!$user) return null;
        return Driver::where('user_id', $user->id)->first();
    }

    // ──────────────────────────────────────────────────────
    // GET /api/driver/schedule
    // Returns list of dates that have shifts for this driver
    // ──────────────────────────────────────────────────────
    public function schedule(Request $request)
    {
        $driver = $this->getDriver();
        if (!$driver) {
            return response()->json(['success' => false, 'message' => 'Driver not found.'], 404);
        }

        // Get shifts via shift_drivers pivot
        $shifts = DriverShift::whereHas('shiftDrivers', function ($q) use ($driver) {
                $q->where('driver_id', $driver->id);
            })
            ->whereBetween('date', [
                Carbon::now()->subDays(7)->toDateString(),
                Carbon::now()->addDays(30)->toDateString(),
            ])
            ->whereNull('deleted_at')
            ->orderBy('date')
            ->orderBy('shift_number')
            ->get(['id', 'date', 'shift_number', 'shift_label', 'start_time', 'end_time', 'status']);

        // Group by date
        $dates = $shifts
            ->groupBy(fn($s) => Carbon::parse($s->date)->toDateString())
            ->keys()
            ->map(fn($date) => ['date' => $date])
            ->values();

        return response()->json([
            'success' => true,
            'data'    => $dates,
        ]);
    }

    // ──────────────────────────────────────────────────────
    // GET /api/driver/schedule/date?date=2026-03-15
    // Returns shifts + rides for a specific date
    // ──────────────────────────────────────────────────────
    public function scheduleByDate(Request $request)
    {
        $driver = $this->getDriver();
        if (!$driver) {
            return response()->json(['success' => false, 'message' => 'Driver not found.'], 404);
        }

        $date = $request->input('date', Carbon::today()->toDateString());

        // Face verification check
        $faceVerifiedUntil = $driver->face_verified_until
            ? Carbon::parse($driver->face_verified_until)
            : null;

        // Get shifts for this driver on this date via shift_drivers
        $shifts = DriverShift::with([
                'shiftRides.ride.rideAssign.subscription.kid',
                'shiftRides.ride.rideAssign.subscription.user',
                'shiftRides.ride.pickupLocation',
                'shiftRides.ride.dropoffLocation',
            ])
            ->whereHas('shiftDrivers', function ($q) use ($driver) {
                $q->where('driver_id', $driver->id);
            })
            ->whereDate('date', $date)
            ->whereNull('deleted_at')
            ->orderBy('shift_number')
            ->get();

        $result = $shifts->map(function ($shift) use ($driver, $faceVerifiedUntil, $date) {
            // Shift end time as Carbon
            $shiftEndDT = Carbon::parse($date . ' ' . $shift->end_time);

            // Is face verified for this shift?
            // Valid if: verified AND verified_until >= shift end time
            $isVerifiedForShift = $faceVerifiedUntil
                && Carbon::now()->lessThanOrEqualTo($faceVerifiedUntil)
                && $faceVerifiedUntil->greaterThanOrEqualTo($shiftEndDT);

            // Rides in this shift — filter to this driver only
            $rides = $shift->shiftRides
                ->filter(fn($sr) => $sr->ride !== null && $sr->ride->driver_id == $driver->id)
                ->sortBy('seat_number')
                ->map(function ($sr) {
                    $ride = $sr->ride;
                    $kid  = $ride->rideAssign?->subscription?->kid;
                    $user = $ride->rideAssign?->subscription?->user;

                    return [
                        'id'               => $ride->id,
                        'seat_number'      => $sr->seat_number,
                        'ride_type'        => $ride->ride_type,
                        'status'           => $ride->status,
                        'date'             => optional($ride->date)->format('Y-m-d'),
                        'pickup_time'      => $ride->pickup
                            ? Carbon::parse($ride->pickup)->format('H:i')
                            : null,
                        'drop_off_time'    => $ride->drop_off
                            ? Carbon::parse($ride->drop_off)->format('H:i')
                            : null,
                        'pickup_location'  => $ride->pickupLocation?->address,
                        'drop_off_location'=> $ride->dropoffLocation?->address,
                        'student_name'     => $kid
                            ? trim($kid->first_name . ' ' . $kid->last_name)
                            : 'Unknown',
                        'parent_name'      => $user
                            ? trim($user->first_name . ' ' . $user->last_name)
                            : null,
                        'parent_phone'     => $user?->phone,
                    ];
                })
                ->values();

            return [
                'shift_id'              => $shift->id,
                'shift_number'          => $shift->shift_number,
                'shift_label'           => $shift->shift_label,
                'start_time'            => $shift->start_time,
                'end_time'              => $shift->end_time,
                'status'                => $shift->status,
                'is_verified_for_shift' => $isVerifiedForShift,
                'ride_count'            => $rides->count(),
                'rides'                 => $rides,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data'    => [
                'date'               => $date,
                'shifts'             => $result,
                'face_verified_until'=> $faceVerifiedUntil?->toDateTimeString(),
                // Legacy flat rides list
                'rides'              => $result->flatMap(fn($s) => $s['rides'])->values(),
            ],
        ]);
    }
}

// ════════════════════════════════════════════════════════
// Add to DriverShift model (app/Models/DriverShift.php):
// ════════════════════════════════════════════════════════
// public function shiftDrivers()
// {
//     return $this->hasMany(\App\Models\ShiftDriver::class, 'driver_shift_id');
// }
