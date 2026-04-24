<?php
// app/Services/SmsService.php

namespace App\Services;

use App\Models\TwilioCredential;

class SmsService
{
    protected TwilioService|ClickSendService $driver;
    protected TwilioCredential $cred;

    public function __construct()
    {
        $cred = TwilioCredential::active();

        if (! $cred) {
            throw new \RuntimeException('No active SMS credential configured.');
        }

        $this->cred   = $cred;

        // ── Auto pick driver based on active provider ──────────
        $this->driver = match($cred->provider) {
            'clicksend' => new ClickSendService($cred),
            default     => new TwilioService($cred),
        };
    }

    public function sendSms(string $to, string $body): array
    {
        return $this->driver->sendSms($to, $body);
    }

    public function validateCredentials(): bool
    {
        return $this->driver->validateCredentials();
    }

    public function getProvider(): string
    {
        return $this->cred->provider;
    }

    public function getMode(): string
    {
        return $this->cred->mode;
    }

    public function getActiveCred(): TwilioCredential
    {
        return $this->cred;
    }
}
