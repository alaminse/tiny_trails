<?php

namespace App\Observers;

use App\Models\Timesheet;
use Modules\RideAssignment\app\Models\Ride;

class RideObserver
{
    public function updated(Ride $ride): void
    {
        // ✅ When ride status changes to 'completed', auto-create timesheet
        if ($ride->isDirty('status') && $ride->status === 'completed') {
            Timesheet::firstOrCreate(
                ['ride_id' => $ride->id],   // prevent duplicates
                [
                    'driver_id'   => $ride->driver_id,
                    'date'        => $ride->date,
                    'shift_start' => $ride->pickup,
                    'shift_end'   => $ride->drop_off,
                    'status'      => 'pending',
                ]
            );
        }
    }
}
