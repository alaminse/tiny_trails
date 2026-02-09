<?php

// in Modules/UserRolePermission/app/Models/Kid.php

namespace Modules\UserRolePermission\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Brick\Math\BigDecimal;
use Modules\Subscription\app\Models\Location; // Make sure to import the Location model

class Kid extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'dob',
        'gender',
        'height_cm',
        'weight_kg',
        'school_name',
        'school_address',
        'pickup_location',
        'dropoff_location',
        'hair_color',
        'eye_color',
        'birthmarks',
        'emergency_contacts',
        'photo',
        'pickup_location_id', // <-- ADD THIS
        'dropoff_location_id', // <-- ADD THIS
        'distance_between_locations',
    ];

    protected $casts = [
        'dob' => 'date',
        'emergency_contacts' => 'array',
        'distance_between_locations' => 'decimal:2',
    ];

    /**
     * Fix for Laravel BigDecimal error where scale is passed as a string.
     */
    protected function asDecimal($value, $decimals)
    {
        return (string) BigDecimal::of($value)->toScale((int) $decimals);
    }

    // Define the relationships
    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    public function dropoffLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'dropoff_location_id');
    }
}
