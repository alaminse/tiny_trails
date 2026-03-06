<?php

namespace Modules\Subscription\app\Models;

use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Model;
use Modules\UserRolePermission\App\Models\Kid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'latitude',
        'longitude',
        'street1',
        'street2',
        'city',
        'state',
        'postal_code',
        'country_code',
        'type',
    ];

    protected $casts = [
        // 'latitude' => 'decimal:8',
        // 'longitude' => 'decimal:8',
        'latitude' => 'float',
        'longitude' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected function asDecimal($value, $decimals)
    {
        // Ensure the scale (decimals) is an integer to prevent the TypeError.
        return (string) BigDecimal::of($value)->toScale((int) $decimals);
    }

    // Relationships
    public function kids()
    {
        return $this->hasMany(Kid::class, 'pickup_location_id');
    }

    public function dropoffKids()
    {
        return $this->hasMany(Kid::class, 'dropoff_location_id');
    }

    // Scope for pickup locations
    public function scopePickup($query)
    {
        return $query->where('type', 'pickup');
    }

    // Scope for dropoff locations
    public function scopeDropoff($query)
    {
        return $query->where('type', 'dropoff');
    }
}
