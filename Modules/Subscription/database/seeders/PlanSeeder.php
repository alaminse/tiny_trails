<?php

namespace Modules\Subscription\database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run()
    {
        DB::table('plans')->delete();

        // We assume PickupTypeSeeder has run
        $standardPickupId = DB::table('pickup_types')->where('name', 'Standard')->first()->id;
        $expressPickupId = DB::table('pickup_types')->where('name', 'Express')->first()->id;

        $plans = [
            [
                'pickup_type_id' => $standardPickupId,
                'name' => 'Basic Monthly',
                'slug' => 'basic-monthly',
                'description' => 'Perfect for occasional rides.',
                'price' => 50.00,
                'sell_price' => 45.00,
                'currency' => 'AUD',
                'interval' => 'month',
                'interval_count' => 1,
                'features' => json_encode(['10 Rides per month', 'Standard Support']),
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'pickup_type_id' => $expressPickupId,
                'name' => 'Premium Monthly',
                'slug' => 'premium-monthly',
                'description' => 'For the frequent rider.',
                'price' => 100.00,
                'sell_price' => 90.00,
                'currency' => 'AUD',
                'interval' => 'month',
                'interval_count' => 1,
                'features' => json_encode(['Unlimited Rides', 'Express Booking', 'Priority Support']),
                'status' => 'active',
                'sort_order' => 2,
            ],
        ];

        DB::table('plans')->insert(array_map(function ($plan) {
            return array_merge($plan, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $plans));
    }
}
