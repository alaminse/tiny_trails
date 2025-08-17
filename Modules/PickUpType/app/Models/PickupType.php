<?php

namespace Modules\PickUpType\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\PickUpType\Database\Factories\PickupTypeFactory;

class PickupType extends Model
{
    use HasFactory, SoftDeletes;


    protected $guarded =['id'];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
    
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' ? 1 : 0;
    }

    public function setIsActiveAttribute($value)
    {
        $this->attributes['status'] = $value == 1 ? 'active' : 'inactive';
    }
}
