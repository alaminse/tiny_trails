<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\UserRolePermission\app\Models\Driver;

class ShiftDriver extends Model
{
    protected $table = 'shift_drivers';

    protected $fillable = [
        'driver_shift_id',
        'driver_id',
        'status',
        'notes',
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(DriverShift::class, 'driver_shift_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
