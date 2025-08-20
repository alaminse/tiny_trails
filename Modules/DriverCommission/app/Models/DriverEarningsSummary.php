<?php

namespace Modules\DriverCommission\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class DriverEarningsSummary extends Model
{
    use HasFactory;

    protected $table = 'driver_earnings_summary';
    protected $fillable = [
        'driver_id',
        'summary_date',
        'summary_type',
        'total_rides',
        'completed_rides',
        'cancelled_rides',
        'total_fare',
        'total_commission',
        'total_bonus',
        'total_penalty',
        'net_earnings',
        'completion_rate',
        'average_rating',
        'total_distance_km',
        'total_duration_minutes',
    ];

    protected $casts = [
        'summary_date' => 'date',
        'total_fare' => 'decimal:2',
        'total_commission' => 'decimal:2',
        'total_bonus' => 'decimal:2',
        'total_penalty' => 'decimal:2',
        'net_earnings' => 'decimal:2',
        'completion_rate' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Accessors & Mutators
    protected function netEarnings(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn () => $this->calculateNetEarnings(),
        );
    }

    protected function completionRate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn () => $this->calculateCompletionRate(),
        );
    }

    // Scopes
    public function scopeByDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeBySummaryType($query, $type)
    {
        return $query->where('summary_type', $type);
    }

    public function scopeDaily($query)
    {
        return $query->where('summary_type', 'daily');
    }

    public function scopeWeekly($query)
    {
        return $query->where('summary_type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('summary_type', 'monthly');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('summary_date', [$startDate, $endDate]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('summary_date', now()->month)
                    ->whereYear('summary_date', now()->year);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('summary_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // Methods
    public function calculateNetEarnings(): float
    {
        return ($this->total_commission ?? 0) + 
               ($this->total_bonus ?? 0) - 
               ($this->total_penalty ?? 0);
    }

    private function calculateCompletionRate(): float
    {
        if ($this->total_rides === 0) {
            return 0;
        }
        
        return round(($this->completed_rides / $this->total_rides) * 100, 2);
    }

    public function getFormattedSummaryDateAttribute(): string
    {
        return $this->summary_date->format('M d, Y');
    }

    public function getFormattedTotalDistanceAttribute(): string
    {
        return number_format($this->total_distance_km) . ' km';
    }

    public function getFormattedTotalDurationAttribute(): string
    {
        $hours = floor($this->total_duration_minutes / 60);
        $minutes = $this->total_duration_minutes % 60;
        
        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    }

    // Static methods
    public static function createOrUpdateSummary(
        int $driverId, 
        Carbon $date, 
        string $summaryType,
        array $data
    ): self {
        return static::updateOrCreate(
            [
                'driver_id' => $driverId,
                'summary_date' => $date,
                'summary_type' => $summaryType,
            ],
            $data
        );
    }

    public static function getDriverSummary(int $driverId, string $summaryType, ?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $query = static::byDriver($driverId)->bySummaryType($summaryType);
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return $query->orderBy('summary_date', 'desc');
    }
}