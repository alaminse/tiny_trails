<?php
// app/Models/TwilioCredential.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwilioCredential extends Model
{
    protected $fillable = [
        'label',
        'provider',
        'account_sid',
        'auth_token',
        'from_number',
        'messaging_service_sid',
        'mode',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function active(): ?self
    {
        return self::where('is_active', true)->first();
    }

    public static function activate(int $id): void
    {
        self::query()->update(['is_active' => false]);
        self::query()->where('id', $id)->update(['is_active' => true]);
    }

    public function getMaskedTokenAttribute(): string
    {
        return substr($this->auth_token, 0, 6) . str_repeat('*', 26);
    }

    public function isTwilio(): bool
    {
        return $this->provider === 'twilio';
    }

    public function isClickSend(): bool
    {
        return $this->provider === 'clicksend';
    }
}
