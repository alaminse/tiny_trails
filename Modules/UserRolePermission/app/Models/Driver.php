<?php

namespace Modules\UserRolePermission\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\UserRolePermission\Database\Factories\DriverFactory;

class Driver extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    // protected static function newFactory(): DriverFactory
    // {
    //     // return DriverFactory::new();
    // }
}
