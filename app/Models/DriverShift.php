<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\UserRolePermission\app\Models\Driver;

class DriverShift extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'shift_number',
        'shift_label',
        'start_time',
        'end_time',
        'max_seats',
        'booked_seats',
        'instant_seats',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ── Shift definitions ──────────────────────────────────
    const SHIFTS = [
        1 => ['icon' => '🌅', 'label' => 'Morning', 'start' => '06:00', 'end' => '14:00'],
        2 => ['icon' => '🌇', 'label' => 'Evening', 'start' => '14:00', 'end' => '22:00'],
        3 => ['icon' => '🌙', 'label' => 'Night',   'start' => '22:00', 'end' => '06:00'],
    ];

    // ── Relationships ──────────────────────────────────────

    /**
     * Drivers assigned to this shift via pivot
     */
    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'shift_drivers', 'driver_shift_id', 'driver_id')
                    ->withPivot('status', 'notes')
                    ->withTimestamps();
    }

    /**
     * Pivot records (shift_drivers)
     */
    public function shiftDrivers()
    {
        return $this->hasMany(ShiftDriver::class, 'driver_shift_id');
    }

    /**
     * Rides assigned to this shift
     */
    public function shiftRides()
    {
        return $this->hasMany(DriverShiftRide::class, 'driver_shift_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helpers ───────────────────────────────────────────

    public function availableSeats(): int
    {
        return max(0, $this->max_seats - $this->booked_seats - $this->instant_seats);
    }

    /**
     * Is this shift overnight? (end time <= start time, e.g. 22:00 → 06:00)
     */
    public function isOvernight(): bool
    {
        return $this->end_time <= $this->start_time;
    }

    /**
     * Check if a given time (H:i) falls within this shift window
     */
    public function containsTime(string $time): bool
    {
        if ($this->isOvernight()) {
            return $time >= $this->start_time || $time < $this->end_time;
        }
        return $time >= $this->start_time && $time < $this->end_time;
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }
}
