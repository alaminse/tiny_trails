<?php

namespace Modules\Subscription\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Modules\Subscription\app\Models\Subscription;
use Modules\Subscription\app\Models\PaywayTransaction;

class PayWayController extends Controller
{
    private $publishableKey;
    private $secretKey;
    private $baseUrl;
    private $merchantId;
    private $bankAccountId;

    public function __construct()
    {
        $this->publishableKey = env('PAYWAY_PUBLISHABLE_KEY');
        $this->secretKey = env('PAYWAY_SECRET_KEY');
        $this->baseUrl = env('PAYWAY_API_BASE_URL', 'https://api.payway.com.au');
        $this->merchantId = env('PAYWAY_MERCHANT_ID', 'TEST');
        $this->bankAccountId = env('PAYWAY_BANK_ACCOUNT_ID', '0000000A');
    }
    /**
     * Create PayWay customer - FINAL CORRECTED VERSION
     */
    private function createPayWayCustomer($singleUseTokenId, $validated)
    {
        try {
            // Only include fields that PayWay API accepts
            $customerData = [
                'singleUseTokenId' => $singleUseTokenId,
                'merchantId' => $this->merchantId,
                'customerName' => trim($validated['billing_name']),
                'emailAddress' => trim($validated['billing_email']),
                'phoneNumber' => $this->formatPhoneNumber($validated['billing_phone']),
                'street1' => trim($validated['billing_address']['street1']),
                'cityName' => trim($validated['billing_address']['city']),
                'state' => $this->mapStateToPayWay($validated['billing_address']['state']),
                'postalCode' => trim($validated['billing_address']['postal_code']),
            ];

            // Add street2 only if it has value
            if (!empty($validated['billing_address']['street2'])) {
                $customerData['street2'] = trim($validated['billing_address']['street2']);
            }

            Log::info('PayWay Customer Request (Corrected):', $customerData);

            $response = Http::withBasicAuth($this->secretKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ])
                ->timeout(30)
                ->asForm()
                ->post("{$this->baseUrl}/rest/v1/customers", $customerData);

            Log::info('PayWay Customer Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => $response->body()
                ];
            }

            return [
                'success' => true,
                'data' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Customer creation error:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Customer creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format Australian phone number (IMPROVED VERSION)
     */
    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return '+61400000000'; // Default Australian mobile number
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Australian phone number formatting
        if (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            // Standard format like 0412345678 -> +61412345678
            return '+61' . substr($phone, 1);
        } elseif (strlen($phone) == 9) {
            // Already without leading 0 like 412345678 -> +61412345678
            return '+61' . $phone;
        } elseif (strlen($phone) == 8) {
            // Landline without area code, add Sydney area code
            return '+6128' . $phone;
        } elseif (strlen($phone) == 12 && substr($phone, 0, 2) == '61') {
            // Already has 61 prefix, just add +
            return '+' . $phone;
        } elseif (strlen($phone) == 13 && substr($phone, 0, 3) == '+61') {
            // Already properly formatted
            return $phone;
        } else {
            // Default: assume it's a mobile number and add +61
            return '+61' . ltrim($phone, '0');
        }
    }

    /**
     * Validate email format specifically for PayWay
     */
    private function validateEmailForPayWay($email)
    {
        // PayWay accepts standard email formats
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email format');
        }

        // Additional PayWay specific validations if needed
        if (strlen($email) > 254) {
            throw new \Exception('Email address too long');
        }

        return $email;
    }





































    public function storeSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'name' => 'required|string|max:255',
            'trial_days' => 'nullable|integer|min:0|max:365',
            'status' => 'required|in:active,inactive',
            'card_number' => 'required|string|min:13|max:19',
            'expiry_month' => 'required|string|size:2',
            'expiry_year' => 'required|string',
            'cvn' => 'required|string|min:3|max:4',
            'cardholder_name' => 'required|string|max:255',
            'billing_name' => 'required|string|max:255',
            'billing_email' => [
                'required',
                'email:rfc,dns',
                'max:254'
            ],
            'billing_phone' => [
                'required',
                'string',
                'regex:/^(\+61|61|0)?[2-478][\d]{8}$/'
            ],
            'billing_address.street1' => 'required|string|max:255',
            'billing_address.street2' => 'nullable|string|max:255',
            'billing_address.city' => 'required|string|max:100',
            'billing_address.state' => 'required|string|max:100',
            'billing_address.postal_code' => 'required|string|max:20',
            'billing_address.country_code' => 'required|string|size:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Additional email validation for PayWay
        try {
            $validated['billing_email'] = $this->validateEmailForPayWay($validated['billing_email']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email validation failed: ' . $e->getMessage()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $user = User::findOrFail($validated['user_id']);
            $plan = DB::table('plans')->where('id', $validated['plan_id'])->first();

            if (!$plan) {
                throw new \Exception('Plan not found');
            }

            // Step 1: Create single-use token
            $tokenResponse = $this->createSingleUseToken($validated);

            if (!$tokenResponse['success']) {
                throw new \Exception($tokenResponse['message']);
            }

            $singleUseTokenId = $tokenResponse['data']['singleUseTokenId'];

            // Step 2: Create PayWay customer
            $customerResponse = $this->createPayWayCustomer($singleUseTokenId, $validated);

            if (!$customerResponse['success']) {
                throw new \Exception('Failed to create PayWay customer: ' . $customerResponse['message']);
            }

            $paywayCustomer = $customerResponse['data'];

            // Step 3: Create subscription in database first (for order number generation)
            $subscription = new Subscription();
            $subscription->user_id = $validated['user_id'];
            $subscription->plan_id = $validated['plan_id'];
            $subscription->name = $validated['name'];
            $subscription->trial_days = $validated['trial_days'] ?? 0;
            $subscription->status = $validated['status'];
            $subscription->payway_customer_id = $paywayCustomer['customerNumber'];
            $subscription->payway_status = 'active';

            // Calculate dates
            $startDate = now();
            if ($subscription->trial_days > 0) {
                $subscription->trial_ends_at = $startDate->copy()->addDays($subscription->trial_days);
                $subscription->ends_at = $subscription->trial_ends_at->copy()->addDays($plan->billing_cycle_days ?? 30);
            } else {
                $subscription->trial_ends_at = null;
                $subscription->ends_at = $startDate->copy()->addDays($plan->billing_cycle_days ?? 30);
            }

            // Extract card details
            if (isset($paywayCustomer['paymentSetup']['creditCard'])) {
                $cardInfo = $paywayCustomer['paymentSetup']['creditCard'];
                $subscription->card_brand = $cardInfo['cardScheme'] ?? null;
                $subscription->card_last_four = substr($cardInfo['cardNumber'], -4);
                $subscription->card_expiration = $validated['expiry_month'] . '/' . substr($validated['expiry_year'], -2);
            } else {
                $subscription->card_brand = $this->detectCardBrand($validated['card_number']);
                $subscription->card_last_four = substr($validated['card_number'], -4);
                $subscription->card_expiration = $validated['expiry_month'] . '/' . substr($validated['expiry_year'], -2);
            }

            $subscription->save();

            // Step 4: Verify the stored card
            $verificationResponse = $this->verifyStoredCard($paywayCustomer['customerNumber'], $subscription->id);

            // Step 5: Process initial payment (if not in trial and plan has price)
            $paymentResponse = null;

            if (($validated['trial_days'] ?? 0) <= 0 && isset($plan->price) && $plan->price > 0) {
                $paymentResponse = $this->processPayment(
                    $paywayCustomer['customerNumber'],
                    $plan->sell_price > 0 ? $plan->sell_price : $plan->price,
                    $this->generateOrderNumber('initial_payment', $subscription->id)
                );

                if (!$paymentResponse['success']) {
                    throw new \Exception('Failed to process initial payment: ' . $paymentResponse['message']);
                }
            }
            // if (($validated['trial_days'] ?? 0) <= 0 && isset($plan->price) && $plan->price > 0) {
            //     $paymentResponse = $this->processPayment(
            //         $paywayCustomer['customerNumber'],
            //         $plan->price,
            //         $this->generateOrderNumber('initial_payment', $subscription->id)
            //     );

            //     if (!$paymentResponse['success']) {
            //         throw new \Exception('Failed to process initial payment: ' . $paymentResponse['message']);
            //     }
            // }

            // Step 6: Record verification transaction
            if ($verificationResponse && $verificationResponse['success']) {
                $this->recordPaywayTransaction(
                    $user->id,
                    $subscription->id,
                    $verificationResponse['data'],
                    'verification'
                );
            }

            // Step 7: Record initial payment transaction
            if ($paymentResponse && $paymentResponse['success']) {
                $this->recordPaywayTransaction(
                    $user->id,
                    $subscription->id,
                    $paymentResponse['data'],
                    'payment'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'payway_customer_id' => $paywayCustomer['customerNumber'],
                    'verification_status' => $verificationResponse['success'] ?? false ? 'verified' : 'failed',
                    'payment_status' => $paymentResponse ? ($paymentResponse['success'] ? 'completed' : 'failed') : 'skipped',
                    'trial_ends_at' => $subscription->trial_ends_at,
                    'ends_at' => $subscription->ends_at,
                    'card_info' => [
                        'brand' => $subscription->card_brand,
                        'last_four' => $subscription->card_last_four,
                        'expiration' => $subscription->card_expiration
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Subscription creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create subscription: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Generate short order number (max 20 characters)
     */
    private function generateOrderNumber($type = 'payment', $subscriptionId = null)
    {
        $prefix = [
            'payment' => 'PAY',
            'initial_payment' => 'INI',
            'recurring_payment' => 'REC',
            'verification' => 'VER',
            'refund' => 'REF'
        ];

        $typePrefix = $prefix[$type] ?? 'TXN';

        if ($subscriptionId) {
            $subId = str_pad($subscriptionId, 4, '0', STR_PAD_LEFT);
            $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            return $typePrefix . $subId . $random; // e.g., INI0001123 (9 chars)
        }

        $timestamp = substr(time(), -6);
        $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        return $typePrefix . $timestamp . $random; // e.g., PAY123456789 (12 chars)
    }

    /**
     * Verify stored card with proper order number
     */
    private function verifyStoredCard($customerNumber, $subscriptionId = null)
    {
        try {
            $verificationData = [
                'customerNumber' => $customerNumber,
                'transactionType' => 'accountVerification',
                'currency' => 'aud',
                'orderNumber' => $this->generateOrderNumber('verification', $subscriptionId),
            ];

            $response = Http::withBasicAuth($this->secretKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ])
                ->timeout(30)
                ->asForm()
                ->post("{$this->baseUrl}/rest/v1/transactions", $verificationData);

            Log::info('PayWay Verification Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => $result['status'] === 'approved',
                    'data' => $result
                ];
            }

            return [
                'success' => false,
                'message' => 'Verification failed: ' . $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Card verification error:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process payment with proper order number
     */
    public function processPayment($customerNumber, $amount, $orderNumber = null)
    {
        try {
            $paymentData = [
                'customerNumber' => $customerNumber,
                'transactionType' => 'payment',
                'principalAmount' => number_format($amount, 2, '.', ''),
                'currency' => 'aud',
                'orderNumber' => $orderNumber ?? $this->generateOrderNumber('payment'),
            ];

            Log::info('PayWay Payment Request:', $paymentData);

            $response = Http::withBasicAuth($this->secretKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ])
                ->timeout(30)
                ->asForm()
                ->post("{$this->baseUrl}/rest/v1/transactions", $paymentData);

            Log::info('PayWay Payment Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Payment failed: ' . $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Payment processing error:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Detect card brand from card number
     */
    private function detectCardBrand($cardNumber)
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        if (preg_match('/^4/', $cardNumber)) {
            return 'visa';
        }

        if (preg_match('/^5[1-5]/', $cardNumber) || preg_match('/^2[2-7]/', $cardNumber)) {
            return 'mastercard';
        }

        if (preg_match('/^3[47]/', $cardNumber)) {
            return 'amex';
        }

        if (preg_match('/^6(?:011|5)/', $cardNumber)) {
            return 'discover';
        }

        return 'unknown';
    }







































    /**
     * Record PayWay transaction in database
     */
    private function recordPaywayTransaction($userId, $subscriptionId, $transactionData, $type = 'payment')
    {
        try {
            $transaction = new PaywayTransaction();
            $transaction->user_id = $userId;
            $transaction->subscription_id = $subscriptionId;
            $transaction->payway_transaction_id = $transactionData['transactionId'];
            $transaction->payway_customer_id = $transactionData['customerNumber'] ?? null;
            $transaction->transaction_type = $type;

            // Handle amount fields
            $transaction->amount = $transactionData['paymentAmount'] ?? $transactionData['principalAmount'] ?? 0;
            $transaction->principal_amount = $transactionData['principalAmount'] ?? null;
            $transaction->surcharge_amount = $transactionData['surchargeAmount'] ?? null;

            $transaction->currency = strtolower($transactionData['currency'] ?? 'aud');
            $transaction->status = $transactionData['status'];
            $transaction->response_code = $transactionData['responseCode'] ?? null;
            $transaction->response_text = $transactionData['responseText'] ?? null;
            $transaction->gateway_response = json_encode($transactionData);
            $transaction->order_number = $transactionData['orderNumber'] ?? null;
            $transaction->receipt_number = $transactionData['receiptNumber'] ?? null;
            $transaction->processed_at = isset($transactionData['transactionDateTime'])
                ? \Carbon\Carbon::parse($transactionData['transactionDateTime'])
                : now();
            $transaction->settlement_date = isset($transactionData['settlementDate'])
                ? \Carbon\Carbon::parse($transactionData['settlementDate'])->format('Y-m-d')
                : null;

            $transaction->save();

            Log::info('PayWay transaction recorded:', [
                'transaction_id' => $transaction->id,
                'payway_transaction_id' => $transaction->payway_transaction_id,
                'type' => $type,
                'status' => $transaction->status
            ]);

            return $transaction;

        } catch (\Exception $e) {
            Log::error('Failed to record PayWay transaction:', [
                'error' => $e->getMessage(),
                'transaction_data' => $transactionData
            ]);
            return null;
        }
    }

    /**
     * Create single-use token
     */
    private function createSingleUseToken($validated)
    {
        try {
            // Format expiry year to 2 digits if it's 4 digits
            $expiryYear = strlen($validated['expiry_year']) == 4
                ? substr($validated['expiry_year'], -2)
                : $validated['expiry_year'];

            $tokenData = [
                'paymentMethod' => 'creditCard',
                'cardNumber' => preg_replace('/\D/', '', $validated['card_number']),
                'expiryDateMonth' => str_pad($validated['expiry_month'], 2, '0', STR_PAD_LEFT),
                'expiryDateYear' => $expiryYear,
                'cvn' => $validated['cvn'],
                'cardholderName' => trim($validated['cardholder_name']),
            ];

            Log::info('PayWay Token Request:', $tokenData);

            $response = Http::withBasicAuth($this->publishableKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ])
                ->timeout(30)
                ->asForm()
                ->post("{$this->baseUrl}/rest/v1/single-use-tokens", $tokenData);

            Log::info('PayWay Token Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Failed to create payment token: ' . $response->body()
                ];
            }

            return [
                'success' => true,
                'data' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Token creation error:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Token creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Record transaction in database
     */
    private function recordTransaction($userId, $subscriptionId, $transactionData, $type = 'payment')
    {
        try {
            $transaction = new PaywayTransaction();
            $transaction->user_id = $userId;
            $transaction->subscription_id = $subscriptionId;
            $transaction->payway_transaction_id = $transactionData['transactionId'];
            $transaction->payway_customer_id = $transactionData['customerNumber'] ?? null;
            $transaction->transaction_type = $type;
            $transaction->amount = $transactionData['paymentAmount'] ?? 0;
            $transaction->currency = $transactionData['currency'] ?? 'aud';
            $transaction->status = $transactionData['status'];
            $transaction->response_code = $transactionData['responseCode'] ?? null;
            $transaction->response_text = $transactionData['responseText'] ?? null;
            $transaction->gateway_response = json_encode($transactionData);
            $transaction->order_number = $transactionData['orderNumber'] ?? null;
            $transaction->processed_at = now();
            $transaction->save();

            return $transaction;

        } catch (\Exception $e) {
            Log::error('Failed to record transaction:', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Map state names to PayWay format
     */
    private function mapStateToPayWay($state)
    {
        $stateMapping = [
            'New South Wales' => 'NSW',
            'Victoria' => 'VIC',
            'Queensland' => 'QLD',
            'Western Australia' => 'WA',
            'South Australia' => 'SA',
            'Tasmania' => 'TAS',
            'Northern Territory' => 'NT',
            'Australian Capital Territory' => 'ACT',
        ];

        return $stateMapping[$state] ?? $state;
    }

    /**
     * Test PayWay connection
     */
    public function testConnection()
    {
        try {
            Log::info('Testing PayWay connection', [
                'publishable_key_present' => !empty($this->publishableKey),
                'secret_key_present' => !empty($this->secretKey),
                'merchant_id' => $this->merchantId,
                'base_url' => $this->baseUrl
            ]);

            // Test with publishable key first
            $response = Http::withBasicAuth($this->publishableKey, '')
                ->timeout(30)
                ->get("{$this->baseUrl}/rest/v1");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'PayWay connection successful',
                    'data' => $response->json()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'PayWay connection failed',
                'error' => $response->json()
            ], 400);

        } catch (\Exception $e) {
            Log::error('PayWay connection exception', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
