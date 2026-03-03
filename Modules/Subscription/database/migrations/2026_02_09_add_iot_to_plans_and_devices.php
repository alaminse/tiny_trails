<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // -----------------------------
        // 1️⃣ Add IoT fields to plans
        // -----------------------------
        Schema::table('plans', function (Blueprint $table) {
            $table->string('plan_tier')->nullable()->after('features');
            $table->enum('iot_level', ['none', 'basic', 'advanced'])->default('none')->after('plan_tier');
            $table->boolean('includes_hardware')->default(false)->after('iot_level');
            $table->decimal('hardware_price', 10, 2)->nullable()->after('includes_hardware');
        });

        // -----------------------------
        // 2️⃣ Create IoT devices table
        // -----------------------------
        Schema::create('iot_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('image')->nullable();
            $table->enum('iot_level', ['basic','advanced']);
            $table->boolean('supports_sos')->default(false);
            $table->boolean('supports_geofence')->default(false);
            $table->string('battery_type')->nullable();
            $table->enum('status', ['active','deprecated'])->default('active');
            $table->timestamps();
        });

        // -----------------------------
        // 3️⃣ Create pivot table plan_iot_devices
        // -----------------------------
        Schema::create('plan_iot_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->foreignId('iot_device_id')->constrained('iot_devices')->cascadeOnDelete();
            $table->boolean('is_included')->default(true);
            $table->decimal('extra_price', 10, 2)->nullable();
            $table->timestamps();
            $table->unique(['plan_id','iot_device_id']);
        });

        $pickupTypeIds = [];

        $pickupTypes = [
            [
                'name' => 'One Way Pickup',
                'amount' => 0,
                'min_notice_minutes' => 30,
                'requires_instant_notification' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Round Trip',
                'amount' => 0,
                'min_notice_minutes' => 60,
                'requires_instant_notification' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Recurring Pickup',
                'amount' => 0,
                'min_notice_minutes' => 120,
                'requires_instant_notification' => 1,
                'status' => 1,
            ],
        ];

        foreach ($pickupTypes as $type) {
            $pickupTypeIds[] = DB::table('pickup_types')->insertGetId([
                ...$type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -----------------------------
        // 4️⃣ Insert default IoT device
        // -----------------------------
        $deviceId = DB::table('iot_devices')->insertGetId([
            'name'              => 'TinyTrails GPS Tracker',
            'model'             => 'TT-GPS-ADV-01',
            'image'             => 'devices/tinytrails-tracker.png',
            'iot_level'         => 'advanced',
            'supports_sos'      => true,
            'supports_geofence' => true,
            'battery_type'      => 'Rechargeable Li-ion',
            'status'            => 'active',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // -----------------------------
        // 5️⃣ Insert default plans
        // -----------------------------
        $plans = [
            [
                'pickup_type_id'    => 1,
                'name'              => 'Per Trip',
                'slug'              => 'per-trip',
                'description'       => 'Single pickup/drop-off, occasional use.',
                'price'             => 15,
                'sell_price'        => 15,
                'currency'          => 'USD',
                'interval'          => 'trip',
                'interval_count'    => 1,
                'features'          => json_encode(['Transport only','Verified driver']),
                'plan_tier'         => 'per_trip',
                'iot_level'         => 'none',
                'includes_hardware' => false,
                'hardware_price'    => null,
                'status'            => 1,
                'sort_order'        => 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'pickup_type_id'    => 2,
                'name'              => 'Monthly Essential',
                'slug'              => 'monthly-essential',
                'description'       => 'GPS tracking with basic trip history.',
                'price'             => 90,
                'sell_price'        => 75,
                'currency'          => 'USD',
                'interval'          => 'month',
                'interval_count'    => 1,
                'features'          => json_encode(['Live GPS tracking','Basic trip history','Parent app access']),
                'plan_tier'         => 'essential',
                'iot_level'         => 'basic',
                'includes_hardware' => false,
                'hardware_price'    => 49,
                'status'            => 1,
                'sort_order'        => 2,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'pickup_type_id'    => 3,
                'name'              => 'Quarterly Essential+',
                'slug'              => 'quarterly-essential-plus',
                'description'       => 'Includes free TinyTrails GPS tracker.',
                'price'             => 270,
                'sell_price'        => 210,
                'currency'          => 'USD',
                'interval'          => 'month',
                'interval_count'    => 3,
                'features'          => json_encode(['Live GPS tracking','Trip history','Free GPS wearable']),
                'plan_tier'         => 'essential',
                'iot_level'         => 'basic',
                'includes_hardware' => true,
                'hardware_price'    => 0,
                'status'            => 1,
                'sort_order'        => 3,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'pickup_type_id'    => 1,
                'name'              => 'Annual Safety+',
                'slug'              => 'annual-safety-plus',
                'description'       => 'Full child safety with GPS, SOS, geofencing and alerts.',
                'price'             => 900,
                'sell_price'        => 699,
                'currency'          => 'USD',
                'interval'          => 'year',
                'interval_count'    => 1,
                'features'          => json_encode([
                    'Live GPS tracking',
                    'Geofencing alerts',
                    'SOS emergency button',
                    'Smart delay alerts',
                    'Route deviation alerts',
                    'Full location timeline',
                    'Free GPS wearable'
                ]),
                'plan_tier'         => 'safety_plus',
                'iot_level'         => 'advanced',
                'includes_hardware' => true,
                'hardware_price'    => 0,
                'status'            => 1,
                'sort_order'        => 4,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ];

        $planIds = [];
        foreach ($plans as $plan) {
            $planIds[] = DB::table('plans')->insertGetId($plan);
        }

        // -----------------------------
        // 6️⃣ Attach device to all IoT-enabled plans
        // -----------------------------
        foreach ($planIds as $index => $planId) {
            if ($plans[$index]['iot_level'] !== 'none') {
                DB::table('plan_iot_devices')->insert([
                    'plan_id'       => $planId,
                    'iot_device_id' => $deviceId,
                    'is_included'   => $plans[$index]['includes_hardware'],
                    'extra_price'   => $plans[$index]['hardware_price'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_iot_devices');
        Schema::dropIfExists('iot_devices');

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['plan_tier','iot_level','includes_hardware','hardware_price']);
        });
    }
};
