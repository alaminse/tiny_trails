<?php

namespace Modules\LocationManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function getStateNameAttribute()
    {
        return $this->state ? $this->state->name : null;
    }

}
