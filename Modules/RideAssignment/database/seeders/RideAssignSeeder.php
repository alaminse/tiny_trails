<?php

namespace Modules\RideAssignment\database\seeders;

use Illuminate\Database\Seeder;
use Modules\RideAssignment\app\Models\RideAssign;

class RideAssignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RideAssign::factory()->count(40)->create();
        $this->command->info('40 ride assigns created.');
    }
}
