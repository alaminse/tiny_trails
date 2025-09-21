<?php

namespace Modules\Subscription\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class PaywayTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'payway_transaction_id',
        'payway_customer_id',
        'transaction_type',
        'amount',
        'currency',
        'status',
        'response_code',
        'response_text',
        'gateway_response',
        'order_number',
        'processed_at',
    ];


    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'processed_at' => 'datetime',
        'settlement_date' => 'date',
    ];


    /**
     * Get the user that owns the transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription that owns the transaction
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Check if transaction is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if transaction is declined
     */
    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get transaction type in human readable format
     */
    public function getTypeDisplayAttribute(): string
    {
        return match($this->transaction_type) {
            'payment' => 'Payment',
            'refund' => 'Refund',
            'verification' => 'Card Verification',
            'preAuth' => 'Pre-Authorization',
            'capture' => 'Capture',
            default => ucfirst($this->transaction_type),
        };
    }

    /**
     * Get status color for display
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'success',
            'declined' => 'danger',
            'pending' => 'warning',
            'voided' => 'secondary',
            default => 'info',
        };
    }
}
