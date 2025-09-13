<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyPayWayWebhook
{
    /**
     * PayWay webhook signature verify করার জন্য
     */
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-PayWay-Signature');
        $webhookSecret = config('payway.webhook_secret');

        if (!$signature || !$webhookSecret) {
            Log::warning('PayWay webhook signature missing');
            return response('Unauthorized', 401);
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('PayWay webhook signature mismatch');
            return response('Invalid signature', 401);
        }

        return $next($request);
    }
}


