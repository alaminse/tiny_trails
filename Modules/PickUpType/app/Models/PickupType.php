<?php

namespace Modules\PickUpType\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PickUpType\database\factories\PickupTypeFactory;

class PickupType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'amount', 'min_notice_minutes', 'requires_instant_notification', 'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'min_notice_minutes' => 'integer',
        'requires_instant_notification' => 'boolean',
    ];


    protected static function newFactory()
    {
        return PickupTypeFactory::new();
    }
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
