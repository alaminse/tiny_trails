<?php

namespace Modules\Subscription\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\PickUpType\App\Models\PickupType;

class Plan extends Model
{
    use SoftDeletes;

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
        'status',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'interval_count' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Plan এর সাথে PickupType এর relation
     */
    public function pickupType(): BelongsTo
    {
        return $this->belongsTo(PickupType::class);
    }

    /**
     * Plan এর সাথে Subscriptions এর relation
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * শুধুমাত্র active plans get করার জন্য scope
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Price cents থেকে dollar এ convert করার জন্য
     */
    public function getPriceInDollarsAttribute()
    {
        return $this->price / 100;
    }

    /**
     * Sell price cents থেকে dollar এ convert করার জন্য
     */
    public function getSellPriceInDollarsAttribute()
    {
        return $this->sell_price / 100;
    }
}
