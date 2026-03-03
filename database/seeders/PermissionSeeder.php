<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'dashboard',
            'create-users',
            'edit-users',
            'delete-users',
            'view-users',
            'list-users',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-roles',
            'list-roles',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',
            'view-permissions',
            'list-permissions',
            'create-kids',
            'edit-kids',
            'delete-kids',
            'view-kids',
            'list-kids',
            'create-drivers',
            'edit-drivers',
            'delete-drivers',
            'view-drivers',
            'list-drivers',
            'create-parents',
            'edit-parents',
            'delete-parents',
            'view-parents',
            'list-parents',
            'create-pickup',
            'edit-pickup',
            'delete-pickup',
            'view-pickup',
            'list-pickup',
            'create-country',
            'edit-country',
            'delete-country',
            'view-country',
            'list-country',
            'create-state',
            'edit-state',
            'delete-state',
            'view-state',
            'list-state',
            'create-city',
            'edit-city',
            'delete-city',
            'view-city',
            'list-city',
            'create-plan',
            'edit-plan',
            'delete-plan',
            'view-plan',
            'list-plan',
            'create-subscription',
            'edit-subscription',
            'delete-subscription',
            'view-subscription',
            'list-subscription',
            'create-rideassign',
            'edit-rideassign',
            'delete-rideassign',
            'view-rideassign',
            'list-rideassign',
            'create-rides',
            'edit-rides',
            'delete-rides',
            'view-rides',
            'list-rides',
            'unassigned-subscription',

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
            'list-driver-shifts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}

