<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\UserRolePermission\app\Models\Driver;

class DriverWage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'driver_id',
        'rate_type',
        'rate_amount',
        'effective_from',
        'effective_to',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to'   => 'date',
        'rate_amount'    => 'decimal:2',
    ];

    // Relationships
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper: get active wage for a driver on a given date
    public static function getActiveWageForDriver(int $driverId, string $date = null): ?self
    {
        $date = $date ?? now()->toDateString();

        return self::where('driver_id', $driverId)
            ->where('status', 'active')
            ->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', $date);
            })
            ->latest('effective_from')
            ->first();
    }
}
