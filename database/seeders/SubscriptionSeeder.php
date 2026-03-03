<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('plans')->truncate();
        DB::table('iot_devices')->truncate();
        DB::table('plan_iot_devices')->truncate();

        // Fetch Pickup Type IDs dynamically
        $standardId = DB::table('pickup_types')->where('name', 'Standard')->value('id');
        $expressId = DB::table('pickup_types')->where('name', 'Express')->value('id');

        // Insert IoT Device
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

        // Insert Plans
        $plans = [
            [
                'pickup_type_id' => $standardId,
                'name' => 'Per Trip',
                'slug' => 'per-trip',
                'price' => 15,
                'sell_price' => 15,
                'currency' => 'USD',
                'interval' => 'trip',
                'interval_count' => 1,
                'features' => json_encode(['Transport only','Verified driver']),
                'plan_tier' => 'per_trip',
                'iot_level' => 'none',
                'includes_hardware' => false,
                'status' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pickup_type_id' => $expressId,
                'name' => 'Annual Safety+',
                'slug' => 'annual-safety-plus',
                'price' => 900,
                'sell_price' => 699,
                'currency' => 'USD',
                'interval' => 'year',
                'interval_count' => 1,
                'features' => json_encode(['Live GPS','SOS','Geofence']),
                'plan_tier' => 'safety_plus',
                'iot_level' => 'advanced',
                'includes_hardware' => true,
                'hardware_price' => 0,
                'status' => 1,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($plans as $plan) {
            $planId = DB::table('plans')->insertGetId($plan);

            if ($plan['iot_level'] !== 'none') {
                DB::table('plan_iot_devices')->insert([
                    'plan_id' => $planId,
                    'iot_device_id' => $deviceId,
                    'is_included' => $plan['includes_hardware'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
