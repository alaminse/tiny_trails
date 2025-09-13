<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\app\Models\Subscription;

class WebhookController extends Controller
{
    /**
     * PayWay webhook handle করার জন্য
     */
    public function handleWebhook(Request $request): Response
    {
        try {
            $payload = $request->all();
            Log::info('PayWay webhook received:', $payload);

            // Webhook event type অনুযায়ী handle করি
            switch ($payload['eventType'] ?? '') {
                case 'payment.successful':
                    $this->handleSuccessfulPayment($payload);
                    break;

                case 'payment.failed':
                    $this->handleFailedPayment($payload);
                    break;

                case 'subscription.cancelled':
                    $this->handleSubscriptionCancelled($payload);
                    break;

                case 'subscription.expired':
                    $this->handleSubscriptionExpired($payload);
                    break;

                default:
                    Log::warning('Unhandled PayWay webhook event:', $payload);
            }

            return response('Webhook handled successfully', 200);

        } catch (\Exception $e) {
            Log::error('PayWay webhook handling failed: ' . $e->getMessage());
            return response('Webhook handling failed', 500);
        }
    }

    /**
     * Successful payment handle করার জন্য
     */
    protected function handleSuccessfulPayment(array $payload): void
    {
        $customerNumber = $payload['customerNumber'] ?? null;
        if (!$customerNumber) return;

        $subscription = Subscription::where('stripe_id', $customerNumber)->first();
        if (!$subscription) return;

        // Subscription status update করি
        $subscription->update([
            'stripe_status' => 'active',
            'status' => 'active',
        ]);

        Log::info("Payment successful for subscription: {$subscription->id}");
    }

    /**
     * Failed payment handle করার জন্য
     */
    protected function handleFailedPayment(array $payload): void
    {
        $customerNumber = $payload['customerNumber'] ?? null;
        if (!$customerNumber) return;

        $subscription = Subscription::where('stripe_id', $customerNumber)->first();
        if (!$subscription) return;

        // Failed payment এর জন্য status update করি
        $subscription->update([
            'stripe_status' => 'past_due',
        ]);

        Log::warning("Payment failed for subscription: {$subscription->id}");

        // এখানে user কে email notification পাঠাতে পারি
        // Mail::to($subscription->user->email)->send(new PaymentFailedMail($subscription));
    }

    /**
     * Subscription cancellation handle করার জন্য
     */
    protected function handleSubscriptionCancelled(array $payload): void
    {
        $customerNumber = $payload['customerNumber'] ?? null;
        if (!$customerNumber) return;

        $subscription = Subscription::where('stripe_id', $customerNumber)->first();
        if (!$subscription) return;

        $subscription->update([
            'status' => 'inactive',
            'stripe_status' => 'cancelled',
            'canceled_at' => now(),
        ]);

        Log::info("Subscription cancelled: {$subscription->id}");
    }

    /**
     * Subscription expiration handle করার জন্য
     */
    protected function handleSubscriptionExpired(array $payload): void
    {
        $customerNumber = $payload['customerNumber'] ?? null;
        if (!$customerNumber) return;

        $subscription = Subscription::where('stripe_id', $customerNumber)->first();
        if (!$subscription) return;

        $subscription->update([
            'status' => 'inactive',
            'stripe_status' => 'expired',
            'ends_at' => now(),
        ]);

        Log::info("Subscription expired: {$subscription->id}");
    }
}
