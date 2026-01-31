<?php

namespace Modules\PickUpType\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PickupTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('pickup_types')->delete();

        $pickupTypes = [
            [
                'name' => 'Standard',
                'amount' => 5.00,
                'min_notice_minutes' => 60,
                'requires_instant_notification' => 0,
                'status' => 'active',
            ],
            [
                'name' => 'Express',
                'amount' => 10.00,
                'min_notice_minutes' => 15,
                'requires_instant_notification' => 1,
                'status' => 'active',
            ],
        ];

        DB::table('pickup_types')->insert(array_map(function ($type) {
            return array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $pickupTypes));
    }
}
