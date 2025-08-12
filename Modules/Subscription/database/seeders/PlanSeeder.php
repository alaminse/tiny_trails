<?php

namespace Modules\Subscription\database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\app\Models\Plan;

// use Modules\Subscription\app\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'The basic plan for individuals.',
                'price' => 1000, // $10.00
                'currency' => 'USD',
                'interval' => 'month',
                'interval_count' => 1,
                'stripe_plan' => 'price_basic_monthly', // Placeholder Stripe ID
                'trial_days' => 14,
                'features' => json_encode(['Feature A', 'Feature B']),
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'description' => 'The standard plan for small teams.',
                'price' => 2500, // $25.00
                'currency' => 'USD',
                'interval' => 'month',
                'interval_count' => 1,
                'stripe_plan' => 'price_standard_monthly',
                'trial_days' => 14,
                'features' => json_encode(['All Basic features', 'Feature C', 'Feature D']),
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'The premium plan for growing businesses.',
                'price' => 24000, // $240.00
                'currency' => 'USD',
                'interval' => 'year',
                'interval_count' => 1,
                'stripe_plan' => 'price_premium_yearly',
                'trial_days' => 30,
                'features' => json_encode(['All Standard features', 'Feature E', 'Priority Support']),
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        // Create the plans in the database.
        foreach ($plans as $planData) {
            Plan::create($planData);
        }}
}
