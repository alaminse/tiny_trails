<?php

namespace Modules\RideAssignment\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RideAssignment\database\factories\RideFactory;
use Modules\Subscription\app\Models\Location;
use Modules\UserRolePermission\app\Models\Driver;
use Modules\UserRolePermission\app\Models\Kid;

class Ride extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rides';

    protected $fillable = [
        'ride_assign_id',
        'driver_id',
        'parent_id',
        'kid_id',
        'pickup_location_id',
        'dropoff_location_id',
        'ride_type',
        'commission',
        'date',
        'pickup',
        'drop_off',
        'end_at',
        'face_verification1',
        'selfie',
        'face_verification2',
        'end_pic',
        'status'
    ];

    protected $casts = [
        'commission' => 'decimal:2',
        'date' => 'date',
        'pickup' => 'datetime:H:i',
        'drop_off' => 'datetime:H:i',
        'end_at' => 'datetime'
    ];

    protected static function newFactory()
    {
        return RideFactory::new();
    }

    public function rideAssign()
    {
        return $this->belongsTo(RideAssign::class);
    }

    public function kid()
    {
        return $this->belongsTo(Kid::class, 'kid_id');
    }

    public function getKidNameAttribute()
    {
        return $this->kid ? $this->kid->first_name. ' ' .$this->kid->last_name  : null;
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function getDriverNameAttribute()
    {
        return $this->driver ? $this->driver->first_name. ' ' .$this->driver->last_name  : null;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function getParentNameAttribute()
    {
        return $this->parent ? $this->parent->first_name. ' ' .$this->parent->last_name  : null;
    }

    public function getParentPhoneAttribute()
    {
        return $this->parent ? $this->parent->phone : null;
    }

    public function driverCommission()
    {
        return $this->hasOne(DriverCommission::class);
    }

    public function pickupLocation()
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    public function dropoffLocation()
    {
        return $this->belongsTo(Location::class, 'dropoff_location_id');
    }
}
