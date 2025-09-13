<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Modules\Subscription\app\Models\Plan;
use Modules\Subscription\app\Models\Subscription;

class PayWayService
{
    protected string $baseUrl;
    protected string $merchantId;
    protected string $publishableKey;
    protected string $secretKey;
    protected string $bankAccountId;
    protected bool $testMode;

    public function __construct()
    {
        $this->baseUrl = config('payway.base_url');
        $this->merchantId = config('payway.merchant_id');
        $this->publishableKey = config('payway.publishable_key');
        $this->secretKey = config('payway.secret_key');
        $this->bankAccountId = config('payway.bank_account_id');
        $this->testMode = config('payway.test_mode');
    }

    /**
     * PayWay API তে HTTP request পাঠানোর জন্য
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): Response
    {
        $url = $this->baseUrl . ltrim($endpoint, '/');

        return Http::withBasicAuth($this->secretKey, '')
            ->withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ])
            ->{$method}($url, $data);
    }

    /**
     * Single-use token তৈরি করার জন্য (credit card এর জন্য)
     */
    public function createCreditCardToken(array $cardData): array
    {
        try {
            $response = $this->makeRequest('POST', 'single-use-tokens', [
                'paymentMethod' => 'creditCard',
                'cardNumber' => $cardData['card_number'],
                'cvn' => $cardData['cvn'],
                'cardholderName' => $cardData['cardholder_name'],
                'expiryDateMonth' => $cardData['expiry_month'],
                'expiryDateYear' => $cardData['expiry_year'],
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'token' => $response->json('singleUseTokenId'),
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message', 'Token creation failed'),
                'errors' => $response->json('data', [])
            ];

        } catch (Exception $e) {
            Log::error('PayWay token creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Token creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Customer তৈরি করার জন্য PayWay এ
     */
    public function createCustomer(User $user, string $token): array
    {
        try {
            $response = $this->makeRequest('POST', 'customers', [
                'singleUseTokenId' => $token,
                'merchantId' => $this->merchantId,
                'bankAccountId' => $this->bankAccountId,
                'customerName' => $user->name,
                'emailAddress' => $user->email,
                'sendEmailReceipts' => config('payway.send_receipts'),
                'phoneNumber' => $user->phone ?? '',
                'street1' => $user->address ?? '',
                'cityName' => $user->city ?? '',
                'state' => $user->state ?? '',
                'postalCode' => $user->postal_code ?? '',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'customer_number' => $response->json('customerNumber'),
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message', 'Customer creation failed'),
                'errors' => $response->json('data', [])
            ];

        } catch (Exception $e) {
            Log::error('PayWay customer creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Customer creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Payment process করার জন্য
     */
    public function processPayment(array $paymentData): array
    {
        try {
            $response = $this->makeRequest('POST', 'transactions', [
                'singleUseTokenId' => $paymentData['token'],
                'customerNumber' => $paymentData['customer_number'] ?? null,
                'transactionType' => $paymentData['transaction_type'] ?? 'payment',
                'principalAmount' => $paymentData['amount'],
                'currency' => $paymentData['currency'] ?? config('payway.currency'),
                'orderNumber' => $paymentData['order_number'],
                'merchantId' => $this->merchantId,
                'bankAccountId' => $this->bankAccountId,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $responseData['transactionId'],
                    'receipt_number' => $responseData['receiptNumber'],
                    'status' => $responseData['status'],
                    'data' => $responseData
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message', 'Payment processing failed'),
                'errors' => $response->json('data', [])
            ];

        } catch (Exception $e) {
            Log::error('PayWay payment processing failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Subscription create করার জন্য
     */
    public function createSubscription(User $user, Plan $plan, string $token): array
    {
        try {
            // প্রথমে customer তৈরি করি PayWay এ
            $customerResult = $this->createCustomer($user, $token);

            if (!$customerResult['success']) {
                return $customerResult;
            }

            $customerNumber = $customerResult['customer_number'];

            // Recurring billing setup করি
            $response = $this->makeRequest('POST', "customers/{$customerNumber}/schedule", [
                'frequency' => $this->mapPlanInterval($plan->interval),
                'nextPaymentDate' => $this->calculateNextPaymentDate($plan),
                'regularPrincipalAmount' => $plan->price,
                'numberOfPayments' => 0, // Unlimited payments
            ]);

            if ($response->successful()) {
                // Database এ subscription save করি
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'name' => 'default',
                    'stripe_id' => $customerNumber, // PayWay customer number হিসেবে use করি
                    'stripe_status' => 'active',
                    'trial_ends_at' => now()->addDays(config('payway.trial_days')),
                    'ends_at' => null, // Unlimited subscription
                    'status' => 'active',
                ]);

                return [
                    'success' => true,
                    'subscription' => $subscription,
                    'customer_number' => $customerNumber,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message', 'Subscription creation failed'),
                'errors' => $response->json('data', [])
            ];

        } catch (Exception $e) {
            Log::error('PayWay subscription creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Subscription creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Subscription cancel করার জন্য
     */
    public function cancelSubscription(Subscription $subscription): array
    {
        try {
            $response = $this->makeRequest('DELETE', "customers/{$subscription->stripe_id}/schedule");

            if ($response->successful()) {
                $subscription->update([
                    'status' => 'inactive',
                    'canceled_at' => now(),
                    'ends_at' => now()->endOfMonth(), // মাসের শেষ পর্যন্ত active রাখি
                ]);

                return [
                    'success' => true,
                    'subscription' => $subscription
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message', 'Subscription cancellation failed')
            ];

        } catch (Exception $e) {
            Log::error('PayWay subscription cancellation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Subscription cancellation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Plan interval কে PayWay frequency তে map করার জন্য
     */
    protected function mapPlanInterval(string $interval): string
    {
        return match($interval) {
            'week' => 'weekly',
            'month' => 'monthly',
            'year' => 'annually',
            default => 'monthly'
        };
    }

    /**
     * Next payment date calculate করার জন্য
     */
    protected function calculateNextPaymentDate(Plan $plan): string
    {
        $trialDays = config('payway.trial_days');

        return match($plan->interval) {
            'week' => now()->addDays($trialDays)->addWeeks($plan->interval_count)->format('d-M-Y'),
            'month' => now()->addDays($trialDays)->addMonths($plan->interval_count)->format('d-M-Y'),
            'year' => now()->addDays($trialDays)->addYears($plan->interval_count)->format('d-M-Y'),
            default => now()->addDays($trialDays)->addMonth()->format('d-M-Y')
        };
    }

    /**
     * Transaction status check করার জন্য
     */
    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $response = $this->makeRequest('GET', "transactions/{$transactionId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Transaction not found'
            ];

        } catch (Exception $e) {
            Log::error('PayWay transaction status check failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Status check failed: ' . $e->getMessage()
            ];
        }
    }
}
