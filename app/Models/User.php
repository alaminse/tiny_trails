<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Modules\LocationManagement\app\Models\City;
use Modules\LocationManagement\app\Models\Country;
use Modules\LocationManagement\app\Models\State;
use Modules\RideAssignment\app\Models\DriverCommission;
use Modules\UserRolePermission\app\Models\Driver;
use Modules\UserRolePermission\app\Models\Kid;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

class User  extends Authenticatable implements CanResetPasswordContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeExcludeAuth($query)
    {
        return $query->where('id', '!=', Auth::id());
    }

    public function scopeParents($query)
    {
        return $query->role('parent'); // uses Spatie role()
    }


    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function getCountryNameAttribute()
    {
        return $this->country ? $this->country->name : null;
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function getStateNameAttribute()
    {
        return $this->state ? $this->state->name : null;
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function getCityNameAttribute()
    {
        return $this->city ? $this->city->name : null;
    }

    /**
     * Scope to get users with driver role
     */
    public function scopeDrivers($query)
    {
        return $query->role('driver');
    }


    /**
     * Scope to get users with admin role
     */
    public function scopeAdmins($query)
    {
        return $query->role('admin');
    }

    /**
     * Scope to get active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope to get verified users
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Driver relationship
     */
    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    /**
     * Parent relationship (if you have a parents table)
     */
    public function parent()
    {
        return $this->hasOne(Parent::class);
    }

    /**
     * Kids relationship (if parent has kids)
     */
    public function kids()
    {
        return $this->hasMany(Kid::class, 'user_id');
    }

    /**
     * Check if user has driver role
     */
    public function isDriver(): bool
    {
        return $this->hasRole('driver');
    }

    /**
     * Check if user has parent role
     */
    public function isParent(): bool
    {
        return $this->hasRole('parent');
    }

    /**
     * Check if user has admin role
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Get user's display name with role
     */
    public function getDisplayNameAttribute(): string
    {
        $roles = $this->getRoleNames()->implode(', ');
        return "{$this->name} ({$roles})";
    }

    /**
     * Get user's primary role
     */
    public function getPrimaryRoleAttribute(): string
    {
        return $this->getRoleNames()->first() ?? 'user';
    }

    // App\Models\User.php
    public function driverCommissions()
    {
        return $this->hasMany(DriverCommission::class, 'driver_id');
    }
}
