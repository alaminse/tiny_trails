<?php

namespace Modules\DriverCommission\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use Modules\DriverCommission\Database\Factories\DriverCommissionFactory;
use Modules\RideAssignment\app\Models\RideAssignment;

class DriverCommission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'driver_id',
        'ride_assignment_id',
        'base_fare',
        'commission_rate',
        'commission_amount',
        'bonus_amount',
        'penalty_amount',
        'total_earning',
        'commission_type',
        'payment_status',
        'earning_date',
        'payment_date',
        'payment_method',
        'payment_reference',
        'bonus_type',
        'penalty_type',
        'description',
        'metadata',
    ];

    protected $casts = [
        'base_fare' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'total_earning' => 'decimal:2',
        'earning_date' => 'date',
        'payment_date' => 'date',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Relationships
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function rideAssignment(): BelongsTo
    {
        return $this->belongsTo(RideAssignment::class);
    }

    // Accessors & Mutators
    protected function totalEarning(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn () => $this->calculateTotalEarning(),
        );
    }

    protected function commissionAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value, $attributes) => $this->calculateCommissionAmount($attributes),
        );
    }

    // Scopes
    public function scopeByDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeByCommissionType($query, $type)
    {
        return $query->where('commission_type', $type);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('earning_date', [$startDate, $endDate]);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('earning_date', now()->month)
                    ->whereYear('earning_date', now()->year);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('earning_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('earning_date', today());
    }

    // Methods
    public function calculateTotalEarning(): float
    {
        return ($this->commission_amount ?? 0) + 
               ($this->bonus_amount ?? 0) - 
               ($this->penalty_amount ?? 0);
    }

    private function calculateCommissionAmount(array $attributes): float
    {
        $baseFare = $attributes['base_fare'] ?? $this->base_fare ?? 0;
        $commissionRate = $attributes['commission_rate'] ?? $this->commission_rate ?? 0;
        
        return round(($baseFare * $commissionRate) / 100, 2);
    }

    public function markAsPaid(string $paymentMethod = null, string $paymentReference = null): bool
    {
        return $this->update([
            'payment_status' => 'paid',
            'payment_date' => now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
        ]);
    }

    public function markAsFailed(string $reason = null): bool
    {
        return $this->update([
            'payment_status' => 'failed',
            'metadata' => array_merge($this->metadata ?? [], [
                'failure_reason' => $reason,
                'failed_at' => now()->toISOString(),
            ]),
        ]);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->payment_status === 'processing';
    }

    public function getFormattedEarningDateAttribute(): string
    {
        return $this->earning_date->format('M d, Y');
    }

    public function getFormattedPaymentDateAttribute(): ?string
    {
        return $this->payment_date?->format('M d, Y');
    }

    // Static methods
    public static function totalEarningsForDriver(int $driverId, ?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = static::byDriver($driverId);
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return $query->sum('total_earning');
    }

    public static function pendingEarningsForDriver(int $driverId): float
    {
        return static::byDriver($driverId)->pending()->sum('total_earning');
    }

    protected static function newFactory()
    {
        return DriverCommissionFactory::new();
    }
}