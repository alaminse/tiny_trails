<?php

namespace Modules\Subscription\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Modules\UserRolePermission\App\Models\Kid;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_id',
        'name',
        'payway_customer_id',
        'payway_subscription_id',
        'payway_status',
        'trial_days',
        'trial_ends_at',
        'ends_at',
        'canceled_at',
        'cancellation_reason',
        'status',
        'card_brand',
        'card_last_four',
        'card_expiration',
        'assign_ride'
    ];

    protected $casts = [
        'trial_days' => 'integer',
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'trial_ends_at',
        'ends_at',
        'canceled_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that owns the subscription
     */
    public function kid(): BelongsTo
    {
        return $this->belongsTo(Kid::class);
    }

    /**
     * Get the plan that the subscription belongs to
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function transection()
    {
        return $this->hasOne(PaywayTransaction::class);
    }

    /**
     * Get all transactions for this subscription
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PaywayTransaction::class);
    }

    /**
     * Get successful payments for this subscription
     */
    public function payments(): HasMany
    {
        return $this->transactions()
                    ->where('transaction_type', 'payment')
                    ->where('status', 'approved');
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' &&
               $this->payway_status === 'active' &&
               is_null($this->canceled_at);
    }

    /**
     * Check if subscription is canceled
     */
    public function isCanceled(): bool
    {
        return !is_null($this->canceled_at);
    }


    /**
     * Cancel the subscription
     */
    public function cancel(string $reason = null): bool
    {
        $this->canceled_at = now();
        $this->cancellation_reason = $reason;
        $this->status = 'inactive';
        $this->payway_status = 'canceled';

        return $this->save();
    }

    /**
     * Reactivate the subscription
     */
    public function reactivate(): bool
    {
        $this->canceled_at = null;
        $this->cancellation_reason = null;
        $this->status = 'active';
        $this->payway_status = 'active';

        return $this->save();
    }

    /**
     * Get formatted card info for display
     */
    public function getCardDisplayAttribute(): string
    {
        if (!$this->card_brand || !$this->card_last_four) {
            return 'No card on file';
        }

        return ucfirst($this->card_brand) . ' ending in ' . $this->card_last_four;
    }


    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->isCanceled()) {
            return 'danger';
        }

        if ($this->onTrial()) {
            return 'info';
        }

        return match($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            default => 'secondary',
        };
    }


    /**
     * Calculate total amount paid
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Get last successful payment
     */
    public function getLastPaymentAttribute()
    {
        return $this->payments()->latest('processed_at')->first();
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->whereNull('canceled_at');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive')
                    ->whereNull('canceled_at');
    }


    /**
     * Scope for canceled subscriptions
     */
    public function scopeCanceled($query)
    {
        return $query->whereNotNull('canceled_at');
    }

    /**
     * Scope for trial subscriptions
     */
    public function scopeOnTrial($query)
    {
        return $query->whereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '>', now());
    }

    public function hasExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    // Also add the isOnTrial method if you haven't already:
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }


    /**
     * Scope for expired trials
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '<=', now());
    }



    /**
     * Check if subscription is on trial (uncomment and fix)
     */
    // public function onTrial(): bool
    // {
    //     return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    // }

    /**
     * Check if trial has ended
     */
    public function trialExpired(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Get days remaining in trial (FIXED)
     */
    public function trialDaysRemaining(): int
    {
        if (!$this->trial_ends_at || !$this->onTrial()) {
            return 0;
        }

        try {
            return max(0, now()->diffInDays($this->trial_ends_at, false));
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Days left in trial (FIXED)
     */
    public function daysLeftInTrial(): int
    {
        if (!$this->trial_ends_at || $this->trial_ends_at->isPast()) {
            return 0;
        }

        try {
            return max(0, now()->diffInDays($this->trial_ends_at, false));
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get subscription status for display (FIXED)
     */
    public function getStatusDisplayAttribute(): string
    {
        if ($this->isCanceled()) {
            return 'Canceled';
        }

        if ($this->onTrial()) {
            return 'Trial (' . $this->trialDaysRemaining() . ' days left)';
        }

        return ucfirst($this->status);
    }

    /**
     * Get next billing date (FIXED)
     */
    public function getNextBillingDateAttribute()
    {
        if ($this->onTrial() && $this->trial_ends_at) {
            return $this->trial_ends_at;
        }

        return $this->ends_at;
    }

    /**
     * Override getStatusAttribute to avoid conflicts (FIXED)
     */
    public function getComputedStatusAttribute(): string
    {
        if ($this->isCanceled()) {
            return 'canceled';
        }

        if ($this->hasExpired()) {
            return 'expired';
        }

        if ($this->isOnTrial()) {
            return 'trial';
        }

        return $this->attributes['status'] ?? 'inactive';
    }


    public function daysUntilExpiry(): ?int
    {
        if (!$this->ends_at) {
            return null;
        }

        try {
            return max(0, now()->diffInDays($this->ends_at, false));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getStatusAttribute(): string
    {
        if ($this->isCanceled()) {
            return 'canceled';
        }

        if ($this->hasExpired()) {
            return 'expired';
        }

        if ($this->isOnTrial()) {
            return 'trial';
        }

        return $this->attributes['status'] ?? 'inactive';
    }



    // In Subscription model
    public function paywayTransactions()
    {
        return $this->hasMany(PaywayTransaction::class);
    }

    public function successfulPayments()
    {
        return $this->paywayTransactions()
                    ->where('transaction_type', 'payment')
                    ->where('status', 'approved');
    }


    public function pickupLocation()
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    public function dropoffLocation()
    {
        return $this->belongsTo(Location::class, 'dropoff_location_id');
    }

}

