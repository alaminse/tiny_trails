<?php
// app/Services/TwilioService.php

namespace App\Services;

use App\Models\TwilioCredential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected TwilioCredential $cred;
    protected string $baseUrl;

    public function __construct()
    {
        $cred = TwilioCredential::active();

        if (! $cred) {
            throw new \RuntimeException('No active Twilio credential configured.');
        }

        $this->cred    = $cred;
        $this->baseUrl = "https://api.twilio.com/2010-04-01/Accounts/{$cred->account_sid}";
    }

    // ── Send SMS ──────────────────────────────────────────────────
    public function sendSms(string $to, string $body): array
    {
        $params = [
            'To'   => $to,
            'Body' => $body,
        ];

        if ($this->cred->messaging_service_sid) {
            $params['MessagingServiceSid'] = $this->cred->messaging_service_sid;
        } else {
            $params['From'] = $this->cred->from_number;
        }

        $response = Http::withBasicAuth(
                $this->cred->account_sid,
                $this->cred->auth_token
            )
            ->asForm()
            ->post("{$this->baseUrl}/Messages.json", $params);

        $data = $response->json();

        if ($response->failed()) {
            Log::error('Twilio SMS failed', $data);
            throw new \RuntimeException(
                'Twilio Error: ' . ($data['message'] ?? 'Unknown error') .
                ' (Code: ' . ($data['code'] ?? 'N/A') . ')'
            );
        }

        Log::info('Twilio SMS sent', ['sid' => $data['sid'], 'to' => $to]);

        return $data;
    }

    // ── Validate credentials (no SMS sent) ───────────────────────
    public function validateCredentials(): bool
    {
        $response = Http::withBasicAuth(
                $this->cred->account_sid,
                $this->cred->auth_token
            )
            ->get("https://api.twilio.com/2010-04-01/Accounts/{$this->cred->account_sid}.json");

        return $response->successful();
    }

    // ── Get account balance ───────────────────────────────────────
    public function getBalance(): array
    {
        $response = Http::withBasicAuth(
                $this->cred->account_sid,
                $this->cred->auth_token
            )
            ->get("{$this->baseUrl}/Balance.json");

        return $response->json();
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
