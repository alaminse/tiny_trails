<?php

namespace Modules\Subscription\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PickUpType\App\Models\PickupType;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'interval_count' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function pickupType()
    {
        return $this->belongsTo(PickupType::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
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

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    public function getFormattedSellPriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->sell_price, 2);
    }

    public function getIntervalDisplayAttribute()
    {
        $count = $this->interval_count > 1 ? $this->interval_count . ' ' : '';
        return $count . ucfirst($this->interval) . ($this->interval_count > 1 ? 's' : '');
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'active' ? 1 : 0;
    }

    public function setIsActiveAttribute($value)
    {
        $this->attributes['status'] = $value == 1 ? 'active' : 'inactive';
    }

    // Mutators
    public function setFeaturesAttribute($value)
    {
        if (is_string($value)) {
            $features = array_map('trim', explode(',', $value));
            $this->attributes['features'] = json_encode(array_filter($features));
        } else {
            $this->attributes['features'] = json_encode($value);
        }
    }

    public function getFeaturesStringAttribute()
    {
        return is_array($this->features) ? implode(', ', $this->features) : '';
    }


    public function activeSubscriptions()
    {
        return $this->hasMany(Subscription::class)->active();
    }
}