<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🔐 Creating roles and permissions...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view vehicles',
            'create vehicles',
            'edit vehicles',
            'delete vehicles',
            'view routes',
            'create routes',
            'edit routes',
            'delete routes',
            'view trips',
            'create trips',
            'edit trips',
            'delete trips',
            'view students',
            'create students',
            'edit students',
            'delete students',
            'view reports',
            'manage system settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $driverRole = Role::firstOrCreate(['name' => 'driver']);
        $parentRole = Role::firstOrCreate(['name' => 'parent']);

        // Assign permissions to roles
        $adminRole->syncPermissions($permissions); // Admin gets all permissions

        $driverRole->syncPermissions([
            'view vehicles',
            'view routes',
            'view trips',
            'edit trips',
            'view students',
        ]);

        $parentRole->syncPermissions([
            'view students',
            'view trips',
            'view routes',
        ]);

        $this->command->info('✅ Roles and permissions created successfully');
    }
}
