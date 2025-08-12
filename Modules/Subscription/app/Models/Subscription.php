<?php

namespace Modules\Subscription\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\LocationManagement\Database\Factories\CountryFactory;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];


    // In Modules\Subscription\app\Models\Subscription.php

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // In Modules\Subscription\app\Models\Subscription.php

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
