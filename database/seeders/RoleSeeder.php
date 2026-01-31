<?php

namespace database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'parent']);
        Role::firstOrCreate(['name' => 'driver']);

        $adminRole->givePermissionTo(Permission::all());
    }
}
