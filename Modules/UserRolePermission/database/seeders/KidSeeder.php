<?php

namespace Modules\UserRolePermission\database\seeders;

use Illuminate\Database\Seeder;
use Modules\UserRolePermission\app\Models\Kid;

class KidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Kid::factory()->count(30)->create();
        $this->command->info('30 kids created.');
    }
}
