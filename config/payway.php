<?php


return [
    /**
     * PayWay API এর base URL
     */
    'base_url' => env('PAYWAY_BASE_URL', 'https://api.payway.com.au/rest/v1/'),

    /**
     * PayWay API credentials
     */
    'merchant_id' => env('PAYWAY_MERCHANT_ID'),
    'publishable_key' => env('PAYWAY_PUBLISHABLE_KEY'),
    'secret_key' => env('PAYWAY_SECRET_KEY'),
    'bank_account_id' => env('PAYWAY_BANK_ACCOUNT_ID'),

    /**
     * Default currency
     */
    'currency' => env('PAYWAY_CURRENCY', 'aud'),

    /**
     * Webhook signing secret
     */
    'webhook_secret' => env('PAYWAY_WEBHOOK_SECRET'),

    /**
     * Test mode
     */
    'test_mode' => env('PAYWAY_TEST_MODE', true),

    /**
     * Retry configuration for failed payments
     */
    'retry_attempts' => 3,
    'retry_delay' => 24, // hours

    /**
     * Email configuration
     */
    'send_receipts' => env('PAYWAY_SEND_RECEIPTS', true),

    /**
     * Supported payment methods
     */
    'payment_methods' => [
        'credit_card' => true,
        'bank_account' => true,
    ],

    /**
     * Trial period configuration
     */
    'trial_days' => env('PAYWAY_TRIAL_DAYS', 14),
];
