<?php

namespace Modules\RideAssignment\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\UserRolePermission\app\Models\Driver;

class DriverCommission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'driver_commissions';

    protected $fillable = [
        'driver_id',
        'ride_id',
        'commission_amount',
        'platform_fee',
        'total_fare',
        'payment_status',
        'paid_at'
    ];

    protected $casts = [
        'commission_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'total_fare' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }
}
