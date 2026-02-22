<?php

namespace App\Services;

use App\Models\ShiftAcceptance;
use App\Models\ShiftBroadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\RideAssignment\app\Models\Ride;
use Modules\UserRolePermission\app\Models\Driver;

class ShiftBroadcastService
{
    /**
     * ✅ BOH broadcasts a ride to available drivers
     */
    public function broadcast(
        int $rideId,
        int $bohUserId,
        int $expiryMinutes = 60,
        ?string $broadcastArea = null   // ✅ এই parameter যোগ করুন
    ): ShiftBroadcast {
        $ride = Ride::with('pickupLocation')->findOrFail($rideId);

        return ShiftBroadcast::create([
            'ride_id' => $rideId,
            'broadcast_area' => $broadcastArea ?? $ride->pickupLocation?->state ?? 'All', // ✅
            'broadcasted_at' => now(),
            'expires_at' => now()->addMinutes($expiryMinutes),
            'status' => 'open',
            'broadcasted_by' => $bohUserId,
        ]);
    }

    /**
     * ✅ Driver accepts a shift — ATOMIC to prevent double booking
     */
    public function accept(int $broadcastId, int $driverId): array
    {
        try {
            return DB::transaction(function () use ($broadcastId, $driverId) {

                // ✅ Lock the row so no other request can accept simultaneously
                $broadcast = ShiftBroadcast::lockForUpdate()->findOrFail($broadcastId);

                // Check if still open
                if (! $broadcast->isOpen()) {
                    return [
                        'success' => false,
                        'message' => 'This shift is no longer available.',
                    ];
                }

                // Check if already taken
                $alreadyTaken = ShiftAcceptance::where('shift_broadcast_id', $broadcastId)->exists();
                if ($alreadyTaken) {
                    return [
                        'success' => false,
                        'message' => 'This shift was just taken by another driver.',
                    ];
                }

                // ✅ Create acceptance record
                ShiftAcceptance::create([
                    'shift_broadcast_id' => $broadcastId,
                    'driver_id' => $driverId,
                    'accepted_at' => now(),
                    'status' => 'accepted',
                ]);

                // ✅ Mark broadcast as filled
                $broadcast->update(['status' => 'filled']);

                // ✅ Assign driver to the ride
                $broadcast->ride->update([
                    'driver_id' => $driverId,
                    'status' => 'scheduled',
                ]);

                // ✅ Update driver availability
                Driver::where('id', $driverId)->update([
                    'availability_status' => 'available',
                ]);

                return [
                    'success' => true,
                    'message' => 'Shift accepted successfully!',
                ];
            });

        } catch (\Exception $e) {
            Log::error('Shift acceptance failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ];
        }
    }

    /**
     * ✅ Auto-expire broadcasts that passed their expiry time
     * Run via: php artisan schedule:run
     */
    public function expireOldBroadcasts(): int
    {
        return ShiftBroadcast::where('status', 'open')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }
}
