<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Modules\LocationManagement\App\Models\City;
use Modules\LocationManagement\App\Models\State;
use Modules\UserRolePermission\App\Models\Driver;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

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

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(State::class);
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
}
