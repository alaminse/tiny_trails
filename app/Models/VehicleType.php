<?php
// app/Models/VehicleType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\UserRolePermission\app\Models\Driver;

class VehicleType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'max_capacity',
        'description',
        'status',
    ];

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
}
