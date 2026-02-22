<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RideAssignment\app\Models\Ride;
use Modules\UserRolePermission\app\Models\Driver;

class Timesheet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'driver_id',
        'ride_id',
        'date',
        'shift_start',
        'shift_end',
        'hours_worked',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'date'        => 'date',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper: approve timesheet
    public function approve(int $adminId): void
    {
        $this->update([
            'status'      => 'approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);
    }

    // Helper: reject timesheet
    public function reject(int $adminId, string $reason = null): void
    {
        $this->update([
            'status'      => 'rejected',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'notes'       => $reason,
        ]);
    }
}
