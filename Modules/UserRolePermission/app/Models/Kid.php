<?php

namespace Modules\UserRolePermission\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\UserRolePermission\Database\Factories\KidFactory;

class Kid extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getParentNameAttribute()
    {
        return $this->parent ? $this->parent->first_name. ' ' .$this->parent->last_name  : null;
    }
}
