<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // ── Dashboard ──────────────────────────────────────────────
            'view-dashboard',
            'view-boh-dashboard',

            // ── User Management (BOH) ──────────────────────────────────
            'list-users',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'restore-users',
            'force-delete-users',       // Super-Admin only

            // ── Roles ──────────────────────────────────────────────────
            'list-roles',
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'restore-roles',
            'force-delete-roles',       // Super-Admin only

            // ── Permissions ────────────────────────────────────────────
            'list-permissions',
            'view-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',
            'assign-permissions',       // Super-Admin only

            // ── Kids Management ────────────────────────────────────────
            'list-kids',
            'view-kids',
            'create-kids',
            'edit-kids',
            'delete-kids',
            'restore-kids',
            'force-delete-kids',        // Super-Admin only

            // ── Driver Management ──────────────────────────────────────
            'list-drivers',
            'view-drivers',
            'create-drivers',
            'edit-drivers',
            'delete-drivers',
            'restore-drivers',
            'force-delete-drivers',     // Super-Admin only

            // ── Parent Management ──────────────────────────────────────
            'list-parents',
            'view-parents',
            'create-parents',
            'edit-parents',
            'delete-parents',
            'restore-parents',
            'force-delete-parents',     // Super-Admin only

            // ── PickUp Type ────────────────────────────────────────────
            'list-pickup',
            'view-pickup',
            'create-pickup',
            'edit-pickup',
            'delete-pickup',

            // ── Location Management: Country ───────────────────────────
            'list-country',
            'view-country',
            'create-country',
            'edit-country',
            'delete-country',

            // ── Location Management: State ─────────────────────────────
            'list-state',
            'view-state',
            'create-state',
            'edit-state',
            'delete-state',

            // ── Location Management: City ──────────────────────────────
            'list-city',
            'view-city',
            'create-city',
            'edit-city',
            'delete-city',

            // ── Subscription: Plans ────────────────────────────────────
            'list-plan',
            'view-plan',
            'create-plan',
            'edit-plan',
            'delete-plan',

            // ── Subscription: Subscriptions ────────────────────────────
            'list-subscription',
            'view-subscription',
            'create-subscription',
            'edit-subscription',
            'delete-subscription',
            'unassign-subscription',

            // ── Ride Assignment ────────────────────────────────────────
            'list-rideassign',
            'view-rideassign',
            'create-rideassign',
            'edit-rideassign',
            'delete-rideassign',

            // ── Rides ──────────────────────────────────────────────────
            'list-rides',
            'view-rides',
            'create-rides',
            'edit-rides',
            'delete-rides',

            // ── Driver Shifts (BOH) ────────────────────────────────────
            'list-driver-shifts',
            'view-driver-shifts',
            'create-driver-shifts',
            'edit-driver-shifts',
            'delete-driver-shifts',

            // ── Shift Broadcasts (BOH) ─────────────────────────────────
            'list-shift-broadcast',
            'create-shift-broadcast',
            'cancel-shift-broadcast',

            // ── Timesheets (BOH) ───────────────────────────────────────
            'list-timesheets',
            'view-timesheets',
            'approve-timesheets',
            'reject-timesheets',

            // ── Attendance ─────────────────────────────────────────────
            'list-attendance',
            'view-attendance',

            // ── Driver Wages (Super-Admin only) ────────────────────────
            'list-driver-wages',
            'view-driver-wages',
            'create-driver-wages',
            'edit-driver-wages',
            'delete-driver-wages',

            // ── Vehicle Types ──────────────────────────────────────────
            'list-vehicle-types',
            'view-vehicle-types',
            'create-vehicle-types',
            'edit-vehicle-types',
            'delete-vehicle-types',
            'assign-vehicle-types',

            // ── Face Verification (BOH — view only) ────────────────────
            'list-face-verification',
            'view-face-verification',

            // ── Trash (Super-Admin permanent delete) ───────────────────
            'view-trash',
            'restore-trash',
            'force-delete-trash',       // Super-Admin only
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        $this->command->info('✅ Permissions seeded: ' . count($permissions) . ' permissions.');
    }
}
