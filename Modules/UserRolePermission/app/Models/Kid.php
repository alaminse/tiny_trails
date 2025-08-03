<?php

namespace Modules\UserRolePermission\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\UserRolePermission\Database\Factories\KidFactory;

class Kid extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    // protected static function newFactory(): KidFactory
    // {
    //     // return KidFactory::new();
    // }
}
