<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RideAssignment\app\Models\Ride;

class ShiftBroadcast extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ride_id',
        'broadcast_area',
        'broadcasted_at',
        'expires_at',
        'status',
        'broadcasted_by',
    ];

    protected $casts = [
        'broadcasted_at' => 'datetime',
        'expires_at'     => 'datetime',
    ];

    // Relationships
    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    public function broadcastedBy()
    {
        return $this->belongsTo(User::class, 'broadcasted_by');
    }

    public function acceptance()
    {
        return $this->hasOne(ShiftAcceptance::class);
    }

    // Helper: is this broadcast still open?
    public function isOpen(): bool
    {
        return $this->status === 'open'
            && $this->expires_at
            && $this->expires_at->isFuture();
    }
}
