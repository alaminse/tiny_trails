<?php

namespace Modules\RideAssignment\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Subscription\app\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\RideAssignment\database\factories\RideAssignFactory;

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

    protected static function newFactory()
    {
        return RideAssignFactory::new();
    }

    /**
     * Rides relationship
     */
    public function rides()
    {
        return $this->hasMany(Ride::class, 'ride_assign_id');
    }

    /**
     * Driver relationship (through rides table)
     */
    public function driver()
    {
        return $this->hasOneThrough(
            User::class,           // Final model
            Ride::class,           // Intermediate model
            'ride_assign_id',      // Foreign key on rides table
            'id',                  // Foreign key on users table
            'id',                  // Local key on ride_assigns table
            'driver_id'            // Local key on rides table
        );
    }

    /**
     * Subscription relationship
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }
}
