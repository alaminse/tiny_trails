<?php
// app/Services/ClickSendService.php

namespace App\Services;

use App\Models\TwilioCredential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClickSendService
{
    protected TwilioCredential $cred;
    protected string $baseUrl = 'https://rest.clicksend.com/v3';

    public function __construct(TwilioCredential $cred)
    {
        $this->cred = $cred;
    }

    // ── Send SMS ──────────────────────────────────────────────
    public function sendSms(string $to, string $body): array
    {
        $response = Http::withBasicAuth(
                $this->cred->account_sid, // ClickSend username
                $this->cred->auth_token   // ClickSend api_key
            )
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/sms/send", [
                'messages' => [
                    [
                        'source' => 'php',
                        'body'   => $body,
                        'to'     => $to,
                        'from'   => $this->cred->from_number, // max 11 chars if alphanumeric
                    ]
                ]
            ]);

        $data = $response->json();

        // ClickSend returns 200 with response_code SUCCESS on success
        if ($response->failed()) {
            Log::error('ClickSend SMS failed', $data);
            throw new \RuntimeException(
                'ClickSend Error: ' . ($data['response_msg'] ?? 'Unknown error')
            );
        }

        // Check application status code
        $responseCode = $data['response_code'] ?? '';
        if ($responseCode !== 'SUCCESS') {
            Log::error('ClickSend SMS not successful', $data);
            throw new \RuntimeException(
                'ClickSend Error: ' . $this->getFriendlyError($responseCode)
            );
        }

        Log::info('ClickSend SMS sent', ['to' => $to]);

        return $data;
    }

    // ── Validate Credentials ──────────────────────────────────
    public function validateCredentials(): bool
    {
        $response = Http::withBasicAuth(
                $this->cred->account_sid,
                $this->cred->auth_token
            )
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->get("{$this->baseUrl}/account");

        return $response->successful();
    }

    // ── Friendly error messages ───────────────────────────────
    private function getFriendlyError(string $code): string
    {
        return match($code) {
            'MISSING_CREDENTIALS'    => 'Username or API key is missing.',
            'INVALID_CREDENTIALS'    => 'Username or API key is incorrect.',
            'ACCOUNT_NOT_ACTIVATED'  => 'ClickSend account is not activated.',
            'INSUFFICIENT_CREDIT'    => 'Not enough ClickSend credits.',
            'INVALID_RECIPIENT'      => 'Invalid phone number.',
            'INVALID_SENDER_ID'      => 'Sender ID invalid. Max 11 chars, no spaces.',
            'COUNTRY_NOT_ENABLED'    => 'This country is not enabled on your account.',
            'THROTTLED'              => 'Duplicate message sent too quickly. Try again.',
            'EMPTY_MESSAGE'          => 'Message body is empty.',
            'TOO_MANY_RECIPIENTS'    => 'Too many recipients in one request.',
            default                  => "Unknown error code: {$code}",
        };
    }
}
