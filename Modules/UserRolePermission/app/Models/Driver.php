<?php

namespace Modules\UserRolePermission\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\UserRolePermission\database\Factories\DriverFactory;

// use Modules\UserRolePermission\Database\Factories\DriverFactory;

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
            'face_embedding', 'face_image', 'is_verified', 'device_token', 'status'
        ];

        protected $casts = [
            'driving_license_expiry' => 'date',
            'car_year' => 'integer',
            'is_verified' => 'boolean',
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
