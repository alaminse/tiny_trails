<?php

namespace Modules\UserRolePermission\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        ];

        protected $casts = [
            'driving_license_expiry' => 'date',
            'car_year' => 'integer',
            'is_verified' => 'boolean',


            // --- NEW Casts ---
            'wwc_expiry_date' => 'date',
            'other_qualifications' => 'array', // Casts JSON to an array
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

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

    /**
     * Get the user that owns the Driver
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getDriverNameAttribute()
    {
        return $this->driver ? $this->driver->first_name. ' ' .$this->driver->last_name  : null;
    }
}
