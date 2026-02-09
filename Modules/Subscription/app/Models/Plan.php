<?php

namespace Modules\Subscription\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\PickUpType\app\Models\PickupType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pickup_type_id',
        'name',
        'slug',
        'description',
        'price',
        'sell_price',
        'currency',
        'interval',
        'interval_count',
        'features',
        'plan_tier',
        'iot_level',
        'includes_hardware',
        'hardware_price',
        'status',
        'sort_order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'sell_price' => 'float',
        'hardware_price' => 'float',
        'includes_hardware' => 'boolean',
        'features' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the pickup type that owns the plan.
     */
    public function pickupType()
    {
        return $this->belongsTo(PickupType::class, 'pickup_type_id');
    }

    /**
     * Get the IoT devices for the plan.
     */
    public function iotDevices()
    {
        return $this->hasMany(PlanIotDevice::class);
    }

    /**
     * Get the IoT devices (through pivot table) for the plan.
     */
    public function devices()
    {
        return $this->belongsToMany(IotDevice::class, 'plan_iot_devices', 'plan_id', 'iot_device_id')
            ->withPivot('is_included', 'extra_price');
    }

    /**
     * Get the subscriptions for the plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the active subscriptions for the plan.
     */
    public function activeSubscriptions()
    {
        return $this->subscriptions()->where('status', 'active');
    }

    /**
     * Get the formatted price attribute.
     */
    public function getFormattedPriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    /**
     * Get the formatted sell price attribute.
     */
    public function getFormattedSellPriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->sell_price, 2);
    }

    /**
     * Get the formatted hardware price attribute.
     */
    public function getFormattedHardwarePriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->hardware_price, 2);
    }

    /**
     * Get the interval display attribute.
     */
    public function getIntervalDisplayAttribute()
    {
        $interval = ucfirst($this->interval);
        if ($this->interval_count > 1) {
            $interval .= 's';
        }
        return $this->interval_count . ' ' . $interval;
    }

    /**
     * Get the features string attribute.
     */
    public function getFeaturesStringAttribute()
    {
        if (is_array($this->features)) {
            return implode(', ', $this->features);
        }

        if (is_string($this->features)) {
            $features = json_decode($this->features, true);
            if (is_array($features)) {
                return implode(', ', $features);
            }
            return $this->features;
        }

        return '';
    }

    /**
     * Get the subscriptions count attribute.
     */
    public function getSubscriptionsCountAttribute()
    {
        return $this->subscriptions()->count();
    }

    /**
     * Get the active subscriptions count attribute.
     */
    public function getActiveSubscriptionsCountAttribute()
    {
        return $this->activeSubscriptions()->count();
    }

    /**
     * Scope a query to only include active plans.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive plans.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
