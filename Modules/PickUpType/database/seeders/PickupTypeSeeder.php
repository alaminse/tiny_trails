<?php

namespace Modules\PickUpType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PickupTypeSeeder extends Seeder
{
    public function run(): void
    {

        $pickupTypes = [
            [
                'name' => 'Standard',
                'amount' => 5.00,
                'min_notice_minutes' => 60,
                'requires_instant_notification' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Express',
                'amount' => 10.00,
                'min_notice_minutes' => 15,
                'requires_instant_notification' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Recurring',
                'amount' => 0,
                'min_notice_minutes' => 120,
                'requires_instant_notification' => 1,
                'status' => 1,
            ],
        ];

        DB::table('pickup_types')->insert(
            array_map(fn($type) => array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]), $pickupTypes)
        );
    }
}
