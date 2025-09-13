<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\UserRolePermission\App\Models\Kid;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'kid_id',
        'device_name',
        'imei',
        'device_type',
        'phone_number',
        'is_active',
        'is_online',
        'battery_level',
        'signal_strength',
        'tracksolid_device_id',
        'last_update_time',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_online' => 'boolean',
        'battery_level' => 'integer',
        'signal_strength' => 'integer',
        'last_update_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the kid that owns this device
     */
    public function kid()
    {
        return $this->belongsTo(Kid::class);
    }

    /**
     * Get all location records for this device
     */
    public function locations()
    {
        return $this->hasMany(DeviceLocation::class)->orderBy('timestamp', 'desc');
    }

    /**
     * Get the latest location for this device
     */
    public function lastLocation()
    {
        return $this->hasOne(DeviceLocation::class)->latestOfMany('timestamp');
    }

    /**
     * Get device status color based on online status
     */
    public function getStatusColorAttribute()
    {
        return $this->is_online ? 'green' : 'red';
    }

    /**
     * Get battery level color based on percentage
     */
    public function getBatteryColorAttribute()
    {
        if ($this->battery_level >= 40) return 'green';
        if ($this->battery_level >= 20) return 'orange';
        return 'red';
    }

    /**
     * Check if device needs attention (low battery or offline)
     */
    public function needsAttention()
    {
        return !$this->is_online || ($this->battery_level && $this->battery_level < 20);
    }

    /**
     * Scope for active devices only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for online devices only
     */
    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    /**
     * Scope for devices by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('device_type', $type);
    }
}
