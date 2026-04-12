<?php
// database/seeders/TwilioPermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TwilioPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view-twilio-credentials',
            'manage-twilio-credentials',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm],
                ['guard_name' => 'web']
            );
        }

        $this->command->info('✅ Twilio permissions created.');

        // Super admin gets everything
        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
            $this->command->info('✅ Assigned all Twilio permissions to super-admin.');
        } else {
            $this->command->warn('⚠️  Role "super-admin" not found.');
        }

        // Admin gets view only
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo('view-twilio-credentials');
            $this->command->info('✅ Assigned view-twilio-credentials to admin.');
        } else {
            $this->command->warn('⚠️  Role "admin" not found.');
        }
    }
}
