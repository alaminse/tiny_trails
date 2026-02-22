<?php

namespace Modules\UserRolePermission\app\Models;

use App\Models\User;
use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. IMPORT THE TRAIT
use Modules\Subscription\app\Models\Location;

class Kid extends Model
{
    use SoftDeletes; // <-- 2. USE THE TRAIT IN THE CLASS

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
        'pickup_location_id',
        'dropoff_location_id',
        'distance_between_locations',
    ];

    // Add `deleted_at` to the dates array to ensure it's handled correctly.
    // This is automatically handled by the SoftDeletes trait in modern Laravel versions,
    // but it's good practice to be explicit if needed.
    protected $dates = [
        'deleted_at'
    ];

    protected $casts = [
        'dob' => 'date',
        'emergency_contacts' => 'array',
        'distance_between_locations' => 'decimal:2',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getParentNameAttribute()
    {
        return $this->parent ? $this->parent->first_name. ' ' .$this->parent->last_name  : null;
    }
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

    /**
     * Get the wages for the kid.
     */
    public function kidWages()
    {
        return $this->hasMany(KidWage::class);
    }

    public function pendingWage()
    {
        return $this->hasOne(KidWage::class)->where('status', 'pending')->latest();
    }

    /**
     * Get the active wage for the kid.
     */
    public function activeWage()
    {
        return $this->hasOne(KidWage::class)
                    // ->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where(function($query) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', now());
                    });
    }
}
