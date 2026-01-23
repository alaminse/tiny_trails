<?php

namespace Modules\RideAssignment\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\UserRolePermission\app\Models\Driver;
use app\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RideLocation extends Model
{
    use HasFactory;

    protected $table = 'ride_locations';

    protected $fillable = [
        'ride_id',
        'driver_id',
        'parent_id',
        'kid_id',
        'student_name',
        'longitude',
        'latitude',
    ];

    protected $casts = [
        'longitude' => 'decimal:8',
        'latitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }
    
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
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
}