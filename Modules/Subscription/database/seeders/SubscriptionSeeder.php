<?php

namespace Modules\Subscription\database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\app\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    public function run()
    {
        Subscription::factory()->count(20)->create();
    }
}
