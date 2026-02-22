<?php
// app/Http/Controllers/Admin/ShiftBroadcastController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftBroadcast;
use App\Services\ShiftBroadcastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\RideAssignment\app\Models\Ride;

class ShiftBroadcastController extends Controller
{
    public function __construct(
        protected ShiftBroadcastService $broadcastService
    ) {}

    public function index()
    {
        $openBroadcasts   = ShiftBroadcast::with('ride.pickupLocation', 'ride.dropoffLocation')
            ->where('status', 'open')
            ->latest()
            ->get();

        $filledBroadcasts = ShiftBroadcast::with('ride.pickupLocation', 'ride.dropoffLocation', 'acceptance.driver.user')
            ->where('status', 'filled')
            ->latest()
            ->paginate(20);

        $expiredBroadcasts = ShiftBroadcast::with('ride.pickupLocation')
            ->where('status', 'expired')
            ->latest()
            ->paginate(20);

        // Unassigned rides for the "New Broadcast" modal
        $unassignedRides = Ride::with('pickupLocation', 'dropoffLocation')
            ->whereNull('driver_id')
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->get();

        $openCount    = $openBroadcasts->count();
        $filledCount  = ShiftBroadcast::where('status', 'filled')->count();
        $expiredCount = ShiftBroadcast::where('status', 'expired')->count();

        return view('backend.shift-broadcast.index', compact(
            'openBroadcasts',
            'filledBroadcasts',
            'expiredBroadcasts',
            'unassignedRides',
            'openCount',
            'filledCount',
            'expiredCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ride_id'        => 'required|exists:rides,id',
            'broadcast_area' => 'nullable|string|max:255',
            'expiry_minutes' => 'nullable|integer|min:10|max:1440',
        ]);

        $this->broadcastService->broadcast(
            rideId:         $request->ride_id,
            bohUserId:      Auth::id(),
            expiryMinutes:  $request->expiry_minutes ?? 60,
            broadcastArea:  $request->broadcast_area,
        );

        return redirect()
            ->route('admin.shift.broadcast.index')
            ->with('success', 'Broadcast sent successfully! Drivers will be notified.');
    }

    public function cancel(Request $request, ShiftBroadcast $broadcast)
    {
        abort_if($broadcast->status !== 'open', 403, 'Only open broadcasts can be cancelled.');

        $broadcast->update(['status' => 'cancelled']);

        return redirect()
            ->route('admin.shift.broadcast.index')
            ->with('success', 'Broadcast cancelled.');
    }

    public function extend(Request $request, ShiftBroadcast $broadcast)
    {
        $request->validate([
            'expiry_minutes' => 'required|integer|min:10|max:1440',
        ]);

        abort_if($broadcast->status !== 'open', 403, 'Only open broadcasts can be extended.');

        $broadcast->update([
            'expires_at' => now()->addMinutes($request->expiry_minutes),
        ]);

        return redirect()
            ->route('admin.shift.broadcast.index')
            ->with('success', 'Broadcast extended.');
    }
}
