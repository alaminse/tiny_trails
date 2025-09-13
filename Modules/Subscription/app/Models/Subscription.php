<?php

namespace Modules\Subscription\app\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_id',
        'name',
        'stripe_id',
        'stripe_status',
        'trial_ends_at',
        'ends_at',
        'canceled_at',
        'cancellation_reason',
        'status',
        'card_brand',
        'card_last_four',
        'card_expiration',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    /**
     * Subscription এর সাথে User এর relation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Subscription এর সাথে Plan এর relation
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * শুধুমাত্র active subscriptions get করার জন্য scope
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($query) {
                        $query->whereNull('ends_at')
                              ->orWhere('ends_at', '>', now());
                    });
    }

    /**
     * Trial period active আছে কিনা check করার জন্য
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Subscription canceled আছে কিনা check করার জন্য
     */
    public function canceled(): bool
    {
        return !is_null($this->canceled_at);
    }

    /**
     * Subscription active আছে কিনা check করার জন্য
     */
    public function active(): bool
    {
        return $this->status === 'active' &&
               (!$this->ends_at || $this->ends_at->isFuture());
    }

    /**
     * Subscription এর বাকি দিন count করার জন্য
     */
    public function daysUntilExpires(): int
    {
        if (!$this->ends_at) {
            return PHP_INT_MAX; // Unlimited
        }

        return now()->diffInDays($this->ends_at, false);
    }
}
