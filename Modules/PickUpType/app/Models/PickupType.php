<?php

namespace Modules\PickUpType\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\PickUpType\Database\Factories\PickupTypeFactory;

class PickupType extends Model
{
    use HasFactory, SoftDeletes;


    protected $guarded =['id'];
}
