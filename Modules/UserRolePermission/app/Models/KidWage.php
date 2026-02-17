<?php

namespace Modules\UserRolePermission\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Subscription\app\Models\Plan;

class KidWage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kid_id',
        'plan_id',
        'price',
        'sell_price',
        'start_date',
        'end_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'sell_price' => 'decimal:2',
    ];

    /**
     * Get the kid that owns the wage.
     */
    public function kid()
    {
        return $this->belongsTo(Kid::class);
    }

    /**
     * Get the plan associated with the wage.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Scope a query to only include active wages.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include pending wages.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include inactive wages.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Check if wage is currently active based on dates.
     */
    public function isCurrentlyActive()
    {
        $now = now();
        $isActive = $this->start_date <= $now;

        if ($this->end_date) {
            $isActive = $isActive && $this->end_date >= $now;
        }

        return $isActive && $this->status === 'active';
    }

    /**
     * Get the discount amount
     */
    public function getDiscountAttribute()
    {
        return $this->price - $this->sell_price;
    }

    /**
     * Get the discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->price == 0) {
            return 0;
        }
        return round((($this->price - $this->sell_price) / $this->price) * 100, 2);
    }
}
