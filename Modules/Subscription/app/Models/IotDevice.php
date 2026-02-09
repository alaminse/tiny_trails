<?php

namespace Modules\Subscription\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IotDevice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'model',
        'manufacturer',
        'price',
        'status',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the plan IoT devices for the IoT device.
     */
    public function planIotDevices()
    {
        return $this->hasMany(PlanIotDevice::class, 'iot_device_id');
    }

    /**
     * Get the plans that include this IoT device.
     */
    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_iot_devices', 'iot_device_id', 'plan_id')
            ->withPivot('is_included', 'extra_price');
    }
}
