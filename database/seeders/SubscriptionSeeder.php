<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Subscription\app\Models\Plan;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('plan_iot_devices')->truncate();
        DB::table('plans')->truncate();
        DB::table('iot_devices')->truncate();

        Schema::enableForeignKeyConstraints();

        // Pickup Types
        $standardId = DB::table('pickup_types')->where('name', 'Standard')->value('id');
        $expressId  = DB::table('pickup_types')->where('name', 'Express')->value('id');

        if (! $standardId || ! $expressId) {
            throw new \Exception('Run PickupTypeSeeder first.');
        }

        // IoT Device
        $deviceId = DB::table('iot_devices')->insertGetId([
            'name' => 'TinyTrails GPS Tracker',
            'model' => 'TT-GPS-ADV-01',
            'iot_level' => 'advanced',
            'supports_sos' => true,
            'supports_geofence' => true,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 🔥 UPDATED PLAN STRUCTURE
        $plans = [

            // 1. PER TRIP
            [
                'pickup_type_id' => $standardId,
                'name' => 'Per Trip',
                'slug' => 'per-trip',
                'price' => 15,
                'sell_price' => 15,
                'currency' => 'USD',
                'interval' => 'trip',
                'interval_count' => 1,
                'features' => [
                    'Transport only',
                    'Verified driver'
                ],
                'plan_tier' => 'per_trip',
                'iot_level' => 'none',
                'includes_hardware' => false,
                'hardware_price' => null,
                'status' => 1,
                'sort_order' => 1,
            ],

            // 2. MONTHLY
            [
                'pickup_type_id' => $standardId,
                'name' => 'Monthly Basic',
                'slug' => 'monthly-basic',
                'price' => 120,
                'sell_price' => 99,
                'currency' => 'USD',
                'interval' => 'month',
                'interval_count' => 1,
                'features' => [
                    'Basic GPS tracking',
                    'Trip history',
                    'Customer support'
                ],
                'plan_tier' => 'monthly',
                'iot_level' => 'basic',
                'includes_hardware' => false,
                'hardware_price' => 20,
                'status' => 1,
                'sort_order' => 2,
            ],

            // 3. QUARTERLY
            [
                'pickup_type_id' => $expressId,
                'name' => 'Quarterly Smart',
                'slug' => 'quarterly-smart',
                'price' => 300,
                'sell_price' => 249,
                'currency' => 'USD',
                'interval' => 'month',
                'interval_count' => 3,
                'features' => [
                    'Live GPS tracking',
                    'Basic geofence',
                    'Trip history',
                    'Free device (basic)'
                ],
                'plan_tier' => 'quarterly',
                'iot_level' => 'basic',
                'includes_hardware' => true,
                'hardware_price' => 0,
                'status' => 1,
                'sort_order' => 3,
            ],

            // 4. ANNUAL (PREMIUM)
            [
                'pickup_type_id' => $expressId,
                'name' => 'Annual Safety+',
                'slug' => 'annual-safety-plus',
                'price' => 900,
                'sell_price' => 699,
                'currency' => 'USD',
                'interval' => 'year',
                'interval_count' => 1,
                'features' => [
                    'Live GPS',
                    'SOS emergency button',
                    'Advanced geofencing',
                    'Real-time alerts',
                    'Full location timeline',
                    'Free premium device'
                ],
                'plan_tier' => 'annual',
                'iot_level' => 'advanced',
                'includes_hardware' => true,
                'hardware_price' => 0,
                'status' => 1,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $planData) {

            $planData['features'] = json_encode($planData['features']);

            $plan = Plan::create($planData);

            // Attach device
            if ($plan->iot_level !== 'none' && $plan->includes_hardware) {
                $plan->devices()->attach($deviceId, [
                    'is_included' => true,
                    'extra_price' => $plan->hardware_price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
