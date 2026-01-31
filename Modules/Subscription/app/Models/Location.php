<?php

namespace Modules\Subscription\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Subscription\Database\Factories\LocationFactory;

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
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected static function newFactory()
    {
        return LocationFactory::new();
    }

    // Subscription এর সাথে relationship
    public function pickupSubscriptions()
    {
        return $this->hasMany(Subscription::class, 'pickup_location_id');
    }

    public function dropoffSubscriptions()
    {
        return $this->hasMany(Subscription::class, 'dropoff_location_id');
    }
}
