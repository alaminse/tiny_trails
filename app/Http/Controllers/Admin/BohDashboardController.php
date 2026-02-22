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
        $drivers = Driver::with(['user', 'vehicleType'])
            ->where('status', 'active')
            ->withCount([
                'rides as today_rides_count' => fn($q) => $q
                    ->whereDate('date', today())
                    ->whereNotIn('status', ['cancelled', 'completed'])
            ])
            ->get();

        $activeDrivers     = $drivers->whereIn('availability_status', ['on_trip', 'available', 'ready_next_batch'])->count();
        $ridesToday        = Ride::whereDate('date', today())->count();
        $pendingBroadcasts = ShiftBroadcast::where('status', 'open')->count();
        $delayedCount      = $drivers->where('availability_status', 'delayed')->count();

        // Alert for any driver on trip more than 40 min without completing
        $delayedAlert = null;
        $delayedDriver = Ride::with('driver.user')
            ->where('status', 'in_progress')
            ->where('updated_at', '<', now()->subMinutes(40))
            ->first();

        if ($delayedDriver) {
            $name = optional($delayedDriver->driver->user)->first_name;
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
