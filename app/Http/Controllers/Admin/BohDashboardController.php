<?php
// app/Http/Controllers/Admin/BohDashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftBroadcast;
use Modules\RideAssignment\app\Models\Ride;
use Modules\UserRolePermission\app\Models\Driver;

class BohDashboardController extends Controller
{
    public function index()
    {
        // --- Fetch active drivers (without the broken withCount) ---
        $drivers = Driver::with(['user', 'vehicleType'])
            ->where('status', 'active')
            ->get();

        // --- Attach today's in-progress ride count per driver manually ---
        $driverIds = $drivers->pluck('id');

        $todayRideCounts = Ride::whereDate('date', today())
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->whereIn('driver_id', $driverIds)
            ->selectRaw('driver_id, COUNT(*) as count')
            ->groupBy('driver_id')
            ->pluck('count', 'driver_id');   // [ driver_id => count ]

        // Stamp each driver with the count so the view can use $driver->today_rides_count
        $drivers->each(function ($driver) use ($todayRideCounts) {
            $driver->today_rides_count = $todayRideCounts->get($driver->id, 0);
        });

        // --- Summary stats ---
        $activeDrivers     = $drivers->whereIn('availability_status', ['on_trip', 'available', 'ready_next_batch'])->count();
        $ridesToday        = Ride::whereDate('date', today())->count();
        $pendingBroadcasts = ShiftBroadcast::where('status', 'open')->count();
        $delayedCount      = $drivers->where('availability_status', 'delayed')->count();

        // --- Alert: driver on trip for > 40 min without completing ---
        $delayedAlert  = null;
        $delayedRide   = Ride::with('driver.user')
            ->where('status', 'in_progress')
            ->where('updated_at', '<', now()->subMinutes(40))
            ->first();

        if ($delayedRide) {
            $name         = optional(optional($delayedRide->driver)->user)->first_name ?? 'Unknown';
            $delayedAlert = "Driver {$name} has been \"On Trip\" for over 40 mins — manual check recommended.";
        }

        return view('backend.boh.dashboard', compact(
            'drivers',
            'activeDrivers',
            'ridesToday',
            'pendingBroadcasts',
            'delayedCount',
            'delayedAlert'
        ));
    }
}
