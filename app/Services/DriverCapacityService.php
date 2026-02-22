<?php

namespace App\Services;

use Modules\RideAssignment\app\Models\Ride;
use Modules\UserRolePermission\app\Models\Driver;

class DriverCapacityService
{
    /**
     * ✅ Check if driver is available for a given date + pickup time
     */
    public function isDriverAvailable(int $driverId, string $date, string $pickupTime): bool
    {
        $driver = Driver::with('vehicleType')->find($driverId);

        if (!$driver || !$driver->vehicleType) {
            return true; // no vehicle type assigned, skip capacity check
        }

        // Count active rides for this driver on this date + timeslot
        $activeRides = Ride::where('driver_id', $driverId)
            ->where('date', $date)
            ->where('pickup', $pickupTime)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->count();

        return $activeRides < $driver->vehicleType->max_capacity;
    }

    /**
     * ✅ Check time buffer — next slot must be 10+ mins after last drop_off
     */
    public function hasTimeBuffer(int $driverId, string $date, string $newPickupTime): bool
    {
        $lastRide = Ride::where('driver_id', $driverId)
            ->where('date', $date)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->orderByDesc('drop_off')
            ->first();

        if (!$lastRide) return true; // no rides yet, available

        // If driver manually signalled ready, bypass buffer check
        $driver = Driver::find($driverId);
        if ($driver && $driver->availability_status === 'ready_next_batch') {
            return true;
        }

        // Check 10 min buffer
        $lastDropOff   = \Carbon\Carbon::parse($date . ' ' . $lastRide->drop_off);
        $newPickup     = \Carbon\Carbon::parse($date . ' ' . $newPickupTime);
        $bufferMinutes = $lastDropOff->diffInMinutes($newPickup, false);

        return $bufferMinutes >= 10;
    }

    /**
     * ✅ Get available drivers for a date + pickup time
     */
    public function getAvailableDrivers(string $date, string $pickupTime): \Illuminate\Support\Collection
    {
        return Driver::with('vehicleType', 'user')
            ->where('status', 'active')
            ->get()
            ->filter(function ($driver) use ($date, $pickupTime) {
                return $this->isDriverAvailable($driver->id, $date, $pickupTime)
                    && $this->hasTimeBuffer($driver->id, $date, $pickupTime);
            });
    }

    /**
     * ✅ Get capacity info for a driver on a date + time
     */
    public function getCapacityInfo(int $driverId, string $date, string $pickupTime): array
    {
        $driver = Driver::with('vehicleType')->find($driverId);

        if (!$driver || !$driver->vehicleType) {
            return [
                'max_capacity'  => null,
                'used_capacity' => 0,
                'available'     => true,
                'message'       => 'No vehicle type assigned',
            ];
        }

        $used = Ride::where('driver_id', $driverId)
            ->where('date', $date)
            ->where('pickup', $pickupTime)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->count();

        $max       = $driver->vehicleType->max_capacity;
        $available = $used < $max;

        return [
            'max_capacity'  => $max,
            'used_capacity' => $used,
            'available'     => $available,
            'message'       => $available
                ? "{$used}/{$max} seats used"
                : "At capacity ({$used}/{$max})",
        ];
    }
}
