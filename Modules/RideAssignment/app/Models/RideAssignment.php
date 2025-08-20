<?php

namespace Modules\RideAssignment\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Modules\DriverCommission\app\Models\DriverCommission;
use Modules\RideAssignment\Database\Factories\RideAssignmentFactory;
use Modules\UserRolePermission\app\Models\Kid;
use Modules\Subscription\app\Models\Subscription;

class RideAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'driver_id',
        'parent_id',
        'kid_id',
        'subscription_id',
        'ride_title',
        'pickup_location',
        'dropoff_location',
        'pickup_latitude',
        'pickup_longitude',
        'dropoff_latitude',
        'dropoff_longitude',
        'ride_date',
        'pickup_time',
        'estimated_dropoff_time',
        'recurring_days',
        'is_recurring',
        'recurring_end_date',
        'distance_km',
        'estimated_duration_minutes',
        'ride_fare',
        'driver_commission',
        'platform_fee',
        'status',
        'ride_type',
        'special_instructions',
        'notes',
        'cancellation_reason',
        'accepted_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancelled_by',
    ];

    protected $casts = [
        'ride_date' => 'date',
        'pickup_time' => 'datetime:H:i',
        'estimated_dropoff_time' => 'datetime:H:i',
        'recurring_end_date' => 'date',
        'recurring_days' => 'array',
        'is_recurring' => 'boolean',
        'pickup_latitude' => 'decimal:8',
        'pickup_longitude' => 'decimal:8',
        'dropoff_latitude' => 'decimal:8',
        'dropoff_longitude' => 'decimal:8',
        'distance_km' => 'decimal:2',
        'ride_fare' => 'decimal:2',
        'driver_commission' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'accepted_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function kid()
    {
        return $this->belongsTo(Kid::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function commissions()
    {
        return $this->hasMany(DriverCommission::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['assigned', 'accepted', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('ride_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('ride_date', '>=', today());
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeForParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('ride_date', [$startDate, $endDate]);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'assigned' => '<span class="badge bg-primary">Assigned</span>',
            'accepted' => '<span class="badge bg-info">Accepted</span>',
            'in_progress' => '<span class="badge bg-warning">In Progress</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            'no_show' => '<span class="badge bg-secondary">No Show</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-light">' . ucfirst($this->status) . '</span>';
    }

    public function getRideTypeDisplayAttribute()
    {
        $types = [
            'one_time' => 'One Time',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'custom' => 'Custom',
        ];

        return $types[$this->ride_type] ?? ucfirst($this->ride_type);
    }

    public function getFormattedPickupTimeAttribute()
    {
        return $this->pickup_time ? $this->pickup_time->format('h:i A') : null;
    }

    public function getFormattedEstimatedDropoffTimeAttribute()
    {
        return $this->estimated_dropoff_time ? $this->estimated_dropoff_time->format('h:i A') : null;
    }

    public function getFormattedRideDateAttribute()
    {
        return $this->ride_date ? $this->ride_date->format('M d, Y') : null;
    }

    public function getFormattedRideFareAttribute()
    {
        return '$' . number_format($this->ride_fare, 2);
    }

    public function getFormattedDriverCommissionAttribute()
    {
        return '$' . number_format($this->driver_commission, 2);
    }

    public function getRecurringDaysDisplayAttribute()
    {
        if (!$this->recurring_days || !is_array($this->recurring_days)) {
            return 'N/A';
        }

        return implode(', ', array_map('ucfirst', $this->recurring_days));
    }

    public function getDurationDisplayAttribute()
    {
        if (!$this->estimated_duration_minutes) {
            return 'N/A';
        }

        $hours = floor($this->estimated_duration_minutes / 60);
        $minutes = $this->estimated_duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }

    // Helper Methods
    public function isActive(): bool
    {
        return in_array($this->status, ['assigned', 'accepted', 'in_progress']);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeAccepted(): bool
    {
        return $this->status === 'assigned';
    }

    public function canBeStarted(): bool
    {
        return $this->status === 'accepted';
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'in_progress';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['assigned', 'accepted']);
    }

    public function isRecurring()
    {
        return $this->is_recurring;
    }

    public function isPastDue(): bool
    {
        if ($this->isCompleted() || $this->isCancelled()) {
            return false;
        }

        $rideDateTime = $this->ride_date->setTimeFromTimeString($this->pickup_time);
        return $rideDateTime->isPast();
    }

    public function isToday(): bool
    {
        return $this->ride_date->isToday();
    }

    public function isTomorrow(): bool
    {
        return $this->ride_date->isTomorrow();
    }

    public function isUpcoming(): bool
    {
        return $this->ride_date->isFuture();
    }

    // Status Methods
    public function accept()
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function start()
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Create commission record
        $this->createCommissionRecord();
    }

    public function cancel($reason = null, $cancelledBy = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'cancelled_by' => $cancelledBy,
        ]);
    }

    public function markAsNoShow()
    {
        $this->update([
            'status' => 'no_show',
            'cancelled_at' => now(),
        ]);
    }

    // Commission Methods
    public function createCommissionRecord()
    {
        if ($this->isCompleted() && $this->driver_commission > 0) {
            DriverCommission::create([
                'driver_id' => $this->driver_id,
                'ride_assignment_id' => $this->id,
                'base_fare' => $this->ride_fare,
                'commission_amount' => $this->driver_commission,
                'total_earning' => $this->driver_commission,
                'commission_type' => 'per_ride',
                'earning_date' => $this->ride_date,
                'description' => "Commission for ride: {$this->ride_title}",
            ]);
        }
    }

    public function calculateCommission($commissionRate = null)
    {
        if (!$commissionRate) {
            // Get driver's default commission rate or use platform default
            $commissionRate = $this->driver->commission_rate ?? 15; // 15% default
        }

        $this->driver_commission = ($this->ride_fare * $commissionRate) / 100;
        $this->platform_fee = $this->ride_fare - $this->driver_commission;
        $this->save();

        return $this->driver_commission;
    }

    // Static Methods
    public static function getStatusOptions()
    {
        return [
            'assigned' => 'Assigned',
            'accepted' => 'Accepted',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'no_show' => 'No Show',
        ];
    }

    public static function getRideTypeOptions()
    {
        return [
            'one_time' => 'One Time',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'custom' => 'Custom',
        ];
    }

    public static function getRecurringDaysOptions()
    {
        return [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];
    }

    protected static function newFactory()
    {
        return RideAssignmentFactory::new();
    }
}