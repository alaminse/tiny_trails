<?php

namespace Modules\Subscription\app\Models;


use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\PickUpType\app\Models\PickupType;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Subscription\database\factories\PlanFactory;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pickup_type_id', 'name', 'slug', 'description', 'price', 'sell_price',
        'currency', 'interval', 'interval_count', 'features', 'status', 'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'features' => 'array', // JSON কে array তে কনভার্ট করা হবে
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
        return PlanFactory::new();
    }

    // Relationships
    public function pickupType(): BelongsTo
    {
        return $this->belongsTo(PickupType::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->subscriptions()->active();
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    public function getFormattedSellPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->sell_price, 2);
    }

    public function getIntervalDisplayAttribute(): string
    {
        $interval = Str::plural($this->interval, $this->interval_count);
        return $this->interval_count > 1
            ? "Every {$this->interval_count} {$interval}"
            : "Every {$interval}";
    }

    public function getSubscriptionsCountAttribute(): int
    {
        return $this->subscriptions()->count();
    }

    public function getActiveSubscriptionsCountAttribute(): int
    {
        return $this->activeSubscriptions()->count();
    }

    // Methods
    public function getPriceInCents(): int
    {
        return (int) ($this->sell_price * 100);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });

        static::updating(function ($plan) {
            if ($plan->isDirty('name') && empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
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

    public function scopeByPickupType($query, $pickupTypeId)
    {
        return $query->where('pickup_type_id', $pickupTypeId);
    }

    public function scopeOrderBySort($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
