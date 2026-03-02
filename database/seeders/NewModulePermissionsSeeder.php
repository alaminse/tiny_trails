<?php
// database/seeders/NewModulePermissionsSeeder.php
//
// Run: php artisan db:seed --class=NewModulePermissionsSeeder

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class NewModulePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ── New permissions ──────────────────────────────────────────
        $permissions = [
            // BoH Dashboard
            'boh-dashboard',

            // Shift Broadcasts
            'list-shift-broadcast',
            'create-shift-broadcast',
            'cancel-shift-broadcast',

            // Timesheets
            'list-timesheets',
            'approve-timesheets',
            'reject-timesheets',

            // Driver Wages  (Super Admin only)
            'list-driver-wages',
            'create-driver-wages',
            'edit-driver-wages',
            'delete-driver-wages',

            // Vehicle Types
            'list-vehicle-types',
            'create-vehicle-types',
            'edit-vehicle-types',
            'delete-vehicle-types',
            'assign-vehicle-types',

            // Face Verification  (view only in admin)
            'list-face-verification',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => 'web']
            );
        }

        // ── Assign to Super Admin role ───────────────────────────────
        $superAdmin = Role::where('name', 'admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // ── Assign subset to Admin/BOH role ─────────────────────────
        $admin = Role::where('name', 'BOH_support')->first();
        if ($admin) {
            $admin->givePermissionTo([
                'boh-dashboard',
                'list-shift-broadcast',
                'create-shift-broadcast',
                'cancel-shift-broadcast',
                'list-timesheets',
                'approve-timesheets',
                'reject-timesheets',
                'list-vehicle-types',
                'list-face-verification',
                'list-driver-shifts',
                // Note: wages NOT included for admin — super-admin only
            ]);
        }

        $this->command->info('✅ New module permissions seeded.');
    }
}
