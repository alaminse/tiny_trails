<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Subscription\app\Models\Plan;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // ✅ Child table first
        DB::table('plan_iot_devices')->truncate();
        DB::table('plans')->truncate();
        DB::table('iot_devices')->truncate();

        Schema::enableForeignKeyConstraints();

        // ✅ Fetch Pickup Type IDs safely
        $standardId = DB::table('pickup_types')
            ->where('name', 'Standard')
            ->value('id');

        $expressId = DB::table('pickup_types')
            ->where('name', 'Express')
            ->value('id');

        if (! $standardId || ! $expressId) {
            throw new \Exception('Pickup types not found. Run PickupTypeSeeder first.');
        }

        // ✅ Insert IoT Device
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
                'features' => ['Transport only', 'Verified driver'],
                'plan_tier' => 'per_trip',
                'iot_level' => 'none',
                'includes_hardware' => false,
                'hardware_price' => null,
                'status' => 1,
                'sort_order' => 1,
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
                'features' => ['Live GPS', 'SOS', 'Geofence'],
                'plan_tier' => 'safety_plus',
                'iot_level' => 'advanced',
                'includes_hardware' => true,
                'hardware_price' => 0,
                'status' => 1,
                'sort_order' => 2,
            ],
        ];

        foreach ($plans as $planData) {
            // Convert features array to JSON if your DB column expects JSON
            if (is_array($planData['features'])) {
                $planData['features'] = json_encode($planData['features']);
            }

            $plan = Plan::create($planData);

            // Attach IoT device if plan has hardware
            if ($plan->iot_level !== 'none' && $plan->includes_hardware) {
                $plan->iotDevices()->attach($deviceId, [
                    'extra_price' => $plan->hardware_price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
