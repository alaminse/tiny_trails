<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'latitude',
        'longitude',
        'address',
        'accuracy',
        'speed',
        'timestamp',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'speed' => 'decimal:2',
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the device that owns this location
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get formatted coordinates
     */
    public function getCoordinatesAttribute()
    {
        return "{$this->latitude}, {$this->longitude}";
    }

    /**
     * Calculate distance from another location (in kilometers)
     */
    public function distanceFrom($latitude, $longitude)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($latitude - $this->latitude);
        $lonDelta = deg2rad($longitude - $this->longitude);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Scope for locations within date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('timestamp', [$startDate, $endDate]);
    }

    /**
     * Scope for recent locations
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('timestamp', '>=', now()->subHours($hours));
    }
}
