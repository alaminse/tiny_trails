<?php

namespace Modules\UserRolePermission\database\Seeders;

use Illuminate\Database\Seeder;
use Modules\UserRolePermission\App\Models\Kid;

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
