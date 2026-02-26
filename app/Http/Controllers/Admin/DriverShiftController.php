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
    // INDEX — Show all shifts for a date
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
            ])
            ->whereDate('date', $date)
            ->orderBy('shift_number')
            ->get();

        $unshiftedCount = Ride::whereDate('date', $date)
            ->whereNull('driver_shift_id')
            ->where('status', 'scheduled')
            ->count();

        $drivers = Driver::with('user', 'vehicleType')
            ->where('status', 'active')
            ->get();

        return view('backend.driver-shifts.index', compact('shifts', 'date', 'unshiftedCount', 'drivers'));
    }

    // ──────────────────────────────────────────────────────
    // CREATE — Show create form
    // ──────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        // Check if shifts already exist for this date
        $existingShifts = DriverShift::whereDate('date', $date)
            ->orderBy('shift_number')
            ->get();

        $unshiftedRides = Ride::with(['pickupLocation', 'dropoffLocation'])
            ->whereDate('date', $date)
            ->whereNull('driver_shift_id')
            ->where('status', 'scheduled')
            ->get()
            ->groupBy(fn($r) => $r->pickup_location_id . '_' . $r->dropoff_location_id);

        return view('backend.driver-shifts.create', compact('date', 'existingShifts', 'unshiftedRides'));
    }

    // ──────────────────────────────────────────────────────
    // STORE — Delete existing + create 3 fresh shifts
    // ──────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'date'                        => 'required|date',
            'shifts'                      => 'required|array',
            'shifts.*.shift_number'       => 'required|integer|between:1,3',
            'shifts.*.start_time'         => 'required|date_format:H:i',
            'shifts.*.end_time'           => 'required|date_format:H:i',
            'shifts.*.instant_seats'      => 'nullable|integer|min:0|max:10',
        ]);

        DB::transaction(function () use ($request) {
            $date = $request->input('date');

            // ── Delete existing shifts for this date (and their ride links) ──
            $existing = DriverShift::whereDate('date', $date)->get();
            foreach ($existing as $shift) {
                // Unlink rides
                Ride::where('driver_shift_id', $shift->id)
                    ->update(['driver_shift_id' => null]);

                // Delete pivot drivers
                DB::table('shift_drivers')->where('driver_shift_id', $shift->id)->delete();

                // Delete shift rides
                DriverShiftRide::where('driver_shift_id', $shift->id)->delete();

                $shift->forceDelete();
            }

            // ── Create 3 fresh shifts ──
            foreach ($request->input('shifts') as $num => $def) {
                $shiftDef = DriverShift::SHIFTS[$def['shift_number']] ?? null;

                DriverShift::create([
                    'date'          => $date,
                    'shift_number'  => $def['shift_number'],
                    'shift_label'   => $shiftDef['label'] ?? "Shift {$def['shift_number']}",
                    'start_time'    => $def['start_time'],
                    'end_time'      => $def['end_time'],
                    'max_seats'     => 999, // global shift — seats are per-driver
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
    // SHOW — Shift detail + assign rides
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
        ])->findOrFail($shiftId);

        // Rides matching this shift's time window, unassigned
        $availableRides = Ride::with([
                'rideAssign.subscription.kid',
                'rideAssign.subscription.user',
                'pickupLocation',
                'dropoffLocation',
            ])
            ->whereDate('date', $shift->date)
            ->whereNull('driver_shift_id')
            ->where('status', 'scheduled')
            ->when(!$shift->isOvernight(), function ($q) use ($shift) {
                $q->whereTime('pickup', '>=', $shift->start_time)
                  ->whereTime('pickup', '<',  $shift->end_time);
            }, function ($q) use ($shift) {
                $q->where(function ($q2) use ($shift) {
                    $q2->whereTime('pickup', '>=', $shift->start_time)
                       ->orWhereTime('pickup', '<',  $shift->end_time);
                });
            })
            ->orderBy('pickup')
            ->get()
            ->groupBy(fn($r) => $r->pickup_location_id . '_' . $r->dropoff_location_id);

        $allDrivers = Driver::with('user', 'vehicleType')
            ->where('status', 'active')
            ->get();

        return view('backend.driver-shifts.show', compact('shift', 'availableRides', 'allDrivers'));
    }

    // ──────────────────────────────────────────────────────
    // ASSIGN RIDE → SHIFT  (AJAX)
    // ──────────────────────────────────────────────────────
    public function assignRide(Request $request, DriverShift $shift)
    {
        $data = $request->validate([
            'ride_id'   => 'required|exists:rides,id',
            'driver_id' => 'required|exists:drivers,id',
        ]);

        // Prevent duplicate
        if (DriverShiftRide::where('driver_shift_id', $shift->id)
                           ->where('ride_id', $data['ride_id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Ride already assigned to this shift.']);
        }

        DB::transaction(function () use ($shift, $data) {
            DriverShiftRide::create([
                'driver_shift_id' => $shift->id,
                'ride_id'         => $data['ride_id'],
                'seat_number'     => DriverShiftRide::where('driver_shift_id', $shift->id)->max('seat_number') + 1,
                'type'            => 'booked',
                'assigned_at'     => now(),
            ]);

            // Link ride to shift and driver
            Ride::where('id', $data['ride_id'])->update([
                'driver_shift_id' => $shift->id,
                'driver_id'       => $data['driver_id'],
            ]);

            $shift->increment('booked_seats');

            // Auto-add driver to shift_drivers if not already there
            ShiftDriver::firstOrCreate([
                'driver_shift_id' => $shift->id,
                'driver_id'       => $data['driver_id'],
            ], ['status' => 'assigned']);
        });

        return response()->json(['success' => true, 'message' => 'Ride assigned successfully.']);
    }

    // ──────────────────────────────────────────────────────
    // REMOVE RIDE FROM SHIFT
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

            $shift->decrement('booked_seats');
        });

        return back()->with('success', 'Ride removed from shift.');
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
    // CONFIRM / DELETE SHIFT
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
