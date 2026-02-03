<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;

    /**
     * যেসব কলামগুলো mass assignable হবে
     */
    protected $fillable = [
        'verifiable_type',
        'verifiable_id',
        'type',
        'code',
        'expires_at',
    ];

    /**
     * এই ভেরিফিকেশন কোডটি কোন মডেলের জন্য (যেমন User, Parent)
     */
    public function verifiable()
    {
        // morphTo() হল morphs() এর ইনভার্স রিলেশনশিপ
        return $this->morphTo();
    }

    /**
     * একটি সাহাযোগী মেথড (Helper Method)
     * একটি নির্দিষ্ট মডেল এবং টাইপের জন্য সর্বশেষ এবং মেয়াদ না পার হওয়া কোডটি খুঁজে বের করতে
     */
    public static function findLatestValid($model, string $type)
    {
        return self::where('verifiable_type', get_class($model))
            ->where('verifiable_id', $model->id)
            ->where('type', $type)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }
}
