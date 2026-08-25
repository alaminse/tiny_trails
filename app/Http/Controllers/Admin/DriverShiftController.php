<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverShift;
use App\Models\DriverShiftRide;
use App\Models\ShiftDriver;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\RideAssignment\app\Models\Ride;
use Modules\UserRolePermission\app\Models\Driver;

class DriverShiftController extends Controller
{
    // ──────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $shifts = DriverShift::with([
                'drivers.user',
                'drivers.vehicleType',
                'shiftRides.ride.rideAssign.subscription.kid',
                'shiftRides.ride.rideAssign.subscription.user',
                'shiftRides.ride.pickupLocation',
                'shiftRides.ride.dropoffLocation',
                'shiftRides.ride.driver',
            ])
            ->whereDate('date', $date)
            ->orderBy('shift_number')
            ->get();

        $unshiftedCount = Ride::whereDate('date', $date)
            ->whereNull('driver_shift_id')
            ->count();

        $drivers = Driver::with('user', 'vehicleType')
            ->where('status', 'active')
            ->get();

        return view('backend.driver-shifts.index', compact('shifts', 'date', 'unshiftedCount', 'drivers'));
    }

    // ──────────────────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $existingShifts = DriverShift::whereDate('date', $date)
            ->orderBy('shift_number')->get();

        $unshiftedRides = Ride::with(['pickupLocation', 'dropoffLocation'])
            ->whereDate('date', $date)
            ->whereNull('driver_shift_id')
            ->get()
            ->groupBy(fn($r) => $r->pickup_location_id . '_' . $r->dropoff_location_id);

        return view('backend.driver-shifts.create', compact('date', 'existingShifts', 'unshiftedRides'));
    }

    // ──────────────────────────────────────────────────────
    // STORE — delete existing + recreate 3 shifts
    // ──────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'date'                   => 'required|date',
            'shifts'                 => 'required|array',
            'shifts.*.shift_number'  => 'required|integer|between:1,3',
            'shifts.*.start_time'    => 'required|date_format:H:i',
            'shifts.*.end_time'      => 'required|date_format:H:i',
            'shifts.*.instant_seats' => 'nullable|integer|min:0|max:10',
        ]);

        DB::transaction(function () use ($request) {
            $date = $request->input('date');

            $existing = DriverShift::whereDate('date', $date)->get();
            foreach ($existing as $shift) {
                Ride::where('driver_shift_id', $shift->id)
                    ->update(['driver_shift_id' => null, 'driver_id' => null]);
                DB::table('shift_drivers')->where('driver_shift_id', $shift->id)->delete();
                DriverShiftRide::where('driver_shift_id', $shift->id)->delete();
                $shift->forceDelete();
            }

            foreach ($request->input('shifts') as $def) {
                $shiftDef = DriverShift::SHIFTS[$def['shift_number']] ?? null;
                DriverShift::create([
                    'date'          => $date,
                    'shift_number'  => $def['shift_number'],
                    'shift_label'   => $shiftDef['label'] ?? "Shift {$def['shift_number']}",
                    'start_time'    => $def['start_time'],
                    'end_time'      => $def['end_time'],
                    'max_seats'     => 0,
                    'booked_seats'  => 0,
                    'instant_seats' => (int) ($def['instant_seats'] ?? 0),
                    'status'        => 'draft',
                    'notes'         => $def['notes'] ?? null,
                    'created_by'    => Auth::id(),
                ]);
            }
        });
        return redirect()
            ->route('admin.driver.shifts.index', ['date' => $request->input('date')])
            ->with('success', '3 shifts created for ' . Carbon::parse($request->input('date'))->format('d M Y'));
    }

    // ──────────────────────────────────────────────────────
    // SHOW — seat grid + available rides
    // ──────────────────────────────────────────────────────
    public function show(int $shiftId)
    {
        $shift = DriverShift::with([
            'drivers.user',
            'drivers.vehicleType',
            'shiftRides.ride.rideAssign.subscription.kid',
            'shiftRides.ride.rideAssign.subscription.user',
            'shiftRides.ride.pickupLocation',
            'shiftRides.ride.dropoffLocation',
            'shiftRides.ride.driver',   // Ride->driver relationship (no .user — driver IS the user or has no user relation)
        ])->findOrFail($shiftId);

        $startTime = Carbon::parse($shift->start_time)->format('H:i:s');
        $endTime   = Carbon::parse($shift->end_time)->format('H:i:s');
        $overnight = $endTime <= $startTime;

        // Unassigned rides in this shift's time window
        $availableRides = Ride::with([
                'rideAssign.subscription.kid',
                'rideAssign.subscription.user',
                'pickupLocation',
                'dropoffLocation',
            ])
            ->whereDate('date', $shift->date)
            ->whereNull('driver_shift_id')
            ->where(function ($q) use ($startTime, $endTime, $overnight) {
                if (!$overnight) {
                    $q->whereTime('pickup', '>=', $startTime)
                      ->whereTime('pickup', '<',  $endTime);
                } else {
                    $q->whereTime('pickup', '>=', $startTime)
                      ->orWhereTime('pickup', '<',  $endTime);
                }
            })
            ->orderBy('pickup')
            ->get()
            ->groupBy(fn($r) => $r->pickup_location_id . '_' . $r->dropoff_location_id);

        // Per-driver seat data
        // driver_shift_rides has NO driver_id column — we identify driver via rides.driver_id
        // Seat is OCCUPIED = ride not completed/cancelled
        // Seat is FREE     = ride completed/cancelled (kid dropped off)
        // seatMap keyed by seat_number for O(1) blade lookup
        $driverSeatData = [];
        foreach ($shift->drivers as $driver) {
            $max = $driver->vehicleType?->max_capacity ?? 4;

            $driverRides = $shift->shiftRides
                ->filter(fn($sr) => $sr->ride?->driver_id == $driver->id)
                ->sortBy('seat_number')
                ->values();

            $used = $driverRides
                ->filter(fn($sr) => !in_array($sr->ride?->status, ['completed', 'cancelled', 'canceled']))
                ->count();

            // Build seat_number => shiftRide map
            $seatMap = [];
            foreach ($driverRides as $sr) {
                $seatMap[$sr->seat_number] = $sr;
            }

            $driverSeatData[$driver->id] = [
                'max'     => $max,
                'used'    => $used,
                'free'    => max(0, $max - $used),
                'rides'   => $driverRides,
                'seatMap' => $seatMap,
            ];
        }

        $allDrivers = Driver::with('user', 'vehicleType')
            ->where('status', 'active')
            ->whereNotNull('vehicle_type_id')
            ->whereHas('vehicleType')
            ->get();

        return view('backend.driver-shifts.show',
            compact('shift', 'availableRides', 'allDrivers', 'driverSeatData'));
    }

    // ──────────────────────────────────────────────────────
    // ASSIGN RIDE → SHIFT (AJAX)
    //
    // Tables updated:
    //   driver_shift_rides → insert row (seat reserved, type = 'scheduled'|'instant')
    //   rides              → driver_shift_id + driver_id
    //   shift_drivers      → auto-add driver to pivot if missing
    //
    // Seat availability:
    //   A seat is FREE if no active ride occupies it at the new ride's time window.
    //   Active = rides.status NOT IN (completed, cancelled, canceled)
    //   Overlap = existing.pickup < new.drop_off AND existing.drop_off > new.pickup
    // ──────────────────────────────────────────────────────
    public function assignRide(Request $request, DriverShift $shift)
    {
        $data = $request->validate([
            'ride_id'     => 'required|exists:rides,id',
            'seat_number' => 'required|integer|min:1',
            'driver_id'   => 'required|exists:drivers,id',
        ]);

        $rideId     = (int) $data['ride_id'];
        $seatNumber = (int) $data['seat_number'];
        $driverId   = (int) $data['driver_id'];

        // Prevent duplicate assignment
        if (DriverShiftRide::where('driver_shift_id', $shift->id)
                           ->where('ride_id', $rideId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Ride already assigned.']);
        }

        $driver   = Driver::with('vehicleType')->findOrFail($driverId);
        $maxSeats = $driver->vehicleType?->max_capacity ?? 4;
        $newRide  = Ride::findOrFail($rideId);

        // Count how many seats this driver has occupied in this shift
        // (rides not yet completed — they are still physically in the vehicle)
        $usedSeats = DB::table('driver_shift_rides')
            ->join('rides', 'rides.id', '=', 'driver_shift_rides.ride_id')
            ->where('driver_shift_rides.driver_shift_id', $shift->id)
            ->where('rides.driver_id', $driverId)
            ->whereNotIn('rides.status', ['completed', 'cancelled', 'canceled'])
            ->count();

        if ($usedSeats >= $maxSeats) {
            return response()->json([
                'success' => false,
                'message' => "Vehicle full — {$usedSeats}/{$maxSeats} seats taken for this driver.",
            ]);
        }

        // Check if this specific seat is free at the new ride's pickup→dropoff window.
        // Seat is TAKEN if another active ride overlaps the same time window.
        // Overlap condition: existing.pickup < new.drop_off AND existing.drop_off > new.pickup
        $seatTaken = DB::table('driver_shift_rides')
            ->join('rides', 'rides.id', '=', 'driver_shift_rides.ride_id')
            ->where('driver_shift_rides.driver_shift_id', $shift->id)
            ->where('driver_shift_rides.seat_number', $seatNumber)
            ->where('rides.driver_id', $driverId)
            ->where('rides.id', '!=', $rideId)
            ->whereNotIn('rides.status', ['completed', 'cancelled', 'canceled'])
            ->where('rides.drop_off', '>',  $newRide->pickup)
            ->where('rides.pickup',   '<',  $newRide->drop_off)
            ->exists();

        if ($seatTaken) {
            return response()->json([
                'success' => false,
                'message' => "Seat {$seatNumber} is already reserved during that time window.",
            ]);
        }

        // Determine type: 'instant' if ride_type is instant, otherwise 'scheduled'
        $type = $newRide->ride_type === 'instant' ? 'instant' : 'scheduled';

        DB::transaction(function () use ($shift, $rideId, $seatNumber, $driverId, $type) {
            // 1. Reserve the seat
            DriverShiftRide::create([
                'driver_shift_id' => $shift->id,
                'ride_id'         => $rideId,
                'seat_number'     => $seatNumber,
                'type'            => $type,
                'assigned_at'     => now(),
            ]);

            // 2. Link ride → shift + driver
            Ride::where('id', $rideId)->update([
                'driver_shift_id' => $shift->id,
                'driver_id'       => $driverId,
            ]);

            // 3. Auto-add driver to shift pivot
            ShiftDriver::firstOrCreate([
                'driver_shift_id' => $shift->id,
                'driver_id'       => $driverId,
            ], ['status' => 'assigned']);
        });

        $usedAfter = DB::table('driver_shift_rides')
            ->join('rides', 'rides.id', '=', 'driver_shift_rides.ride_id')
            ->where('driver_shift_rides.driver_shift_id', $shift->id)
            ->where('rides.driver_id', $driverId)
            ->whereNotIn('rides.status', ['completed', 'cancelled', 'canceled'])
            ->count();

        return response()->json([
            'success'         => true,
            'message'         => "Seat {$seatNumber} reserved ({$newRide->pickup}–{$newRide->drop_off}).",
            'seat_number'     => $seatNumber,
            'seats_used'      => $usedAfter,
            'seats_total'     => $maxSeats,
            'seats_available' => max(0, $maxSeats - $usedAfter),
        ]);
    }

    // ──────────────────────────────────────────────────────
    // REMOVE RIDE — frees seat back
    // ──────────────────────────────────────────────────────
    public function removeRide(DriverShift $shift, int $rideId)
    {
        DB::transaction(function () use ($shift, $rideId) {
            DriverShiftRide::where('driver_shift_id', $shift->id)
                           ->where('ride_id', $rideId)
                           ->delete();

            Ride::where('id', $rideId)->update([
                'driver_shift_id' => null,
                'driver_id'       => null,
            ]);
        });

        return back()->with('success', 'Ride removed — seat is now free.');
    }

    // ──────────────────────────────────────────────────────
    // ADD / REMOVE DRIVER FROM SHIFT
    // ──────────────────────────────────────────────────────
    public function addDriver(Request $request, DriverShift $shift)
    {
        $data = $request->validate(['driver_id' => 'required|exists:drivers,id']);
        ShiftDriver::firstOrCreate([
            'driver_shift_id' => $shift->id,
            'driver_id'       => $data['driver_id'],
        ], ['status' => 'assigned']);
        return back()->with('success', 'Driver added to shift.');
    }

    public function removeDriver(DriverShift $shift, Driver $driver)
    {
        ShiftDriver::where('driver_shift_id', $shift->id)
                   ->where('driver_id', $driver->id)
                   ->delete();
        return back()->with('success', 'Driver removed from shift.');
    }

    // ──────────────────────────────────────────────────────
    // CONFIRM / DESTROY
    // ──────────────────────────────────────────────────────
    public function confirm(DriverShift $shift)
    {
        $shift->update(['status' => 'confirmed']);
        return back()->with('success', 'Shift confirmed.');
    }

    public function destroy(DriverShift $shift)
    {
        DB::transaction(function () use ($shift) {
            Ride::where('driver_shift_id', $shift->id)
                ->update(['driver_shift_id' => null, 'driver_id' => null]);
            DB::table('shift_drivers')->where('driver_shift_id', $shift->id)->delete();
            DriverShiftRide::where('driver_shift_id', $shift->id)->delete();
            $shift->forceDelete();
        });
        return redirect()
            ->route('admin.driver.shifts.index', ['date' => $shift->date->toDateString()])
            ->with('success', 'Shift deleted.');
    }
}
