<?php

namespace Modules\UserRolePermission\app\Models;

use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\RideAssignment\app\Models\Ride;
use Modules\UserRolePermission\database\factories\DriverFactory;

class Driver extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'driving_license_number', 'driving_license_expiry', 'driving_license_image',
        'car_model', 'car_make', 'car_year', 'car_color', 'car_plate_number', 'car_image',
        'face_embedding', 'face_image', 'is_verified', 'device_token', 'status',

        // --- NEW Licence Details ---
        'licence_card_number',
        'licence_type',

        // --- NEW Licence Address ---
        'licence_address_line_1',
        'licence_address_line_2',
        'licence_city',
        'licence_state',
        'licence_postal_code',
        'licence_country',

        // --- NEW Compliance Documents ---
        'wwc_card_number',
        'wwc_expiry_date',
        'wwc_card_image',
        'police_clearance_ref',
        'police_clearance_image',
        'other_qualifications',

        // ... existing fields ...
        'vehicle_type_id',
        'availability_status',
        'face_verified_at',
        'face_verified_until',
    ];

    protected $casts = [
        'driving_license_expiry' => 'date',
        'car_year'      => 'integer',
        'is_verified'   => 'integer',

        // --- NEW Casts ---
        'wwc_expiry_date'       => 'date',
        'other_qualifications'  => 'array', // Casts JSON to an array
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'face_embedding'      => 'array',
        'face_verified_at'    => 'datetime',
        'face_verified_until' => 'datetime',
    ];

    // ── Accessor: isVerified ──────────────────────────────
    // true হবে যদি face_verified_until আজকের মধ্যে থাকে
    public function getIsVerifiedAttribute(): bool
    {
        if (empty($this->face_verified_until)) {
            return false;
        }

        return \Carbon\Carbon::now()->lessThanOrEqualTo(
            \Carbon\Carbon::parse($this->face_verified_until)
        );
    }

    // ── Scope: verified drivers only ─────────────────────
    public function scopeVerifiedToday($query)
    {
        return $query->where('face_verified_until', '>=', now());
    }

    // এই মেথডটি খুবই গুরুত্বপূর্ণ
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // Laravel কে বলে দিন যে এই মডেলের জন্য কোন ফ্যাক্টরি ব্যবহার করতে হবে
        return DriverFactory::new();
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getDriverNameAttribute()
    {
        return $this->driver ? $this->driver->first_name.' '.$this->driver->last_name : null;
    }

    // Relationship
    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    // Helper: check if face verification is still valid
    public function isFaceVerified(): bool
    {
        return $this->face_verified_until && $this->face_verified_until->isFuture();
    }

    // Helper: check if driver is at capacity for a given timeslot
    public function isAtCapacityFor(string $date, string $pickupTime): bool
    {
        if (! $this->vehicleType) {
            return false;
        }

        $activeRidesCount = Ride::where('driver_id', $this->id)
            ->where('date', $date)
            ->where('pickup', $pickupTime)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->count();

        return $activeRidesCount >= $this->vehicleType->max_capacity;
    }

    public function getFaceVerificationStatusAttribute()
    {
        if (!$this->is_verified) {
            return 'unverified';
        }

        if (!$this->face_verified_until) {
            return 'expired';
        }

        if (now()->gt($this->face_verified_until)) {
            return 'expired';
        }

        if (now()->diffInMinutes($this->face_verified_until, false) <= 30) {
            return 'expiring';
        }

        return 'verified';
    }

    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class, 'driver_id');
    }

    // Driver model এ user() relation
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
