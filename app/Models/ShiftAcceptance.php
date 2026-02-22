<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\UserRolePermission\app\Models\Driver;

class ShiftAcceptance extends Model
{
    protected $fillable = [
        'shift_broadcast_id',
        'driver_id',
        'accepted_at',
        'status',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    // Relationships
    public function shiftBroadcast()
    {
        return $this->belongsTo(ShiftBroadcast::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
