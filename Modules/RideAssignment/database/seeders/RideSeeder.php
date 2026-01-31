<?php

namespace Modules\RideAssignment\database\seeders;

use Illuminate\Database\Seeder;
use Modules\RideAssignment\app\Models\Ride;

class RideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ride::factory()->count(100)->create();
        $this->command->info('100 rides created.');
    }
}
