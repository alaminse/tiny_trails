<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\RideAssignment\app\Models\Ride;

class DriverShiftRide extends Model
{
    protected $fillable = [
        'driver_shift_id', 'ride_id', 'seat_number', 'type', 'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function driverShift()
    {
        return $this->belongsTo(DriverShift::class);
    }

    public function ride()
    {
        return $this->belongsTo(Ride::class)->withTrashed();
    }
}
