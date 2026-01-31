<?php

namespace database\seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\app\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // LocationFactory ব্যবহার করে 50টি এলোমেলো লোকেশন তৈরি করা হচ্ছে
        Location::factory()->count(50)->create();
        $this->command->info('50 locations created.');
    }
}
