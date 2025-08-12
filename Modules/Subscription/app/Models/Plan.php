<?php

namespace Modules\Subscription\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    // In Modules\Subscription\app\Models\Plan.php

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
