<?php

namespace Modules\UserRolePermission\database\Seeders;

use Illuminate\Database\Seeder;
use Modules\UserRolePermission\app\Models\Driver;

class DriverSeeder extends Seeder
{
    public function run()
    {
        Driver::factory()
            ->count(15)
            ->create()
            ->each(function ($driver) {
                $driver->user->assignRole('driver'); // Assign driver role to the associated user
            });
    }
}
