<?php

namespace Modules\RideAssignment\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Subscription\app\Models\Subscription;

class RideAssign extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ride_assigns';

    protected $fillable = [
        'subscription_id',
        'fare',
        'driver_commission',
        'platform_commission',
        'service_type',
        'total_days',
        'selected_dates',
        'status'
    ];

    protected $casts = [
        'fare' => 'decimal:2',
        'driver_commission' => 'decimal:2',
        'platform_commission' => 'decimal:2',
        'total_days' => 'integer'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }
}
