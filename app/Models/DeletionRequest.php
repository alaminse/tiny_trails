<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class DeletionRequest extends Model
{
    use HasFactory;

    protected $table = 'account_deletion_requests';

    protected $fillable = [
        'email',
        'reason',
        'comments',
        'status',
        'processed_at',
        'processed_by',
        'ip_address',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    // ─── Status Constants ──────────────────────────────────
    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED  = 'completed';
    const STATUS_CANCELLED  = 'cancelled';

    // ─── Reason Labels ─────────────────────────────────────
    const REASONS = [
        'no-longer-use'          => 'I no longer use the app',
        'privacy-concerns'       => 'Privacy concerns',
        'better-alternative'     => 'Found a better alternative',
        'too-many-notifications' => 'Too many notifications',
        'other'                  => 'Other',
    ];

    // ─── Accessors ─────────────────────────────────────────

    /**
     * Human-readable status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED  => 'Completed',
            self::STATUS_CANCELLED  => 'Cancelled',
            default                 => 'Unknown',
        };
    }

    /**
     * Bootstrap badge color for status
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING    => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_COMPLETED  => 'success',
            self::STATUS_CANCELLED  => 'secondary',
            default                 => 'secondary',
        };
    }

    /**
     * Human-readable reason label
     */
    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? ucfirst($this->reason);
    }

    // ─── Scopes ────────────────────────────────────────────

    /**
     * Only pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Only completed requests
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Search by email
     */
    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', 'like', "%{$email}%");
    }

    // ─── Relationships ─────────────────────────────────────

    /**
     * Admin user who processed this request
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
