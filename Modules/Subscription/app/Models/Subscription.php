<?php

namespace Modules\Subscription\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Database\Factories\SubscriptionFactory;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $dates = ['deleted_at'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
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

    public function scopeOnTrial($query)
    {
        return $query->whereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('ends_at')
                    ->where('ends_at', '<', now());
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' ? 1 : 0;
    }

    public function setIsActiveAttribute($value)
    {
        $this->attributes['status'] = $value == 1 ? 'active' : 'inactive';
    }

    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'active':
                return '<span class="badge bg-success">Active</span>';
            case 'inactive':
                return '<span class="badge bg-secondary">Inactive</span>';
            default:
                return '<span class="badge bg-warning">Unknown</span>';
        }
    }

    public function getStripeStatusBadgeAttribute()
    {
        switch ($this->stripe_status) {
            case 'active':
                return '<span class="badge bg-success">Active</span>';
            case 'canceled':
                return '<span class="badge bg-danger">Canceled</span>';
            case 'incomplete':
                return '<span class="badge bg-warning">Incomplete</span>';
            case 'incomplete_expired':
                return '<span class="badge bg-danger">Incomplete Expired</span>';
            case 'past_due':
                return '<span class="badge bg-warning">Past Due</span>';
            case 'trialing':
                return '<span class="badge bg-info">Trialing</span>';
            case 'unpaid':
                return '<span class="badge bg-danger">Unpaid</span>';
            default:
                return '<span class="badge bg-secondary">' . ucfirst($this->stripe_status) . '</span>';
        }
    }

    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function hasExpired()
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function isCanceled()
    {
        return !is_null($this->canceled_at);
    }
    
    protected static function newFactory()
    {
        return SubscriptionFactory::new();
    }
}