<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {

        // ─────────────────────────────────────────────────────────────
        // Permission map per role
        // ─────────────────────────────────────────────────────────────
        $rolePermissions = [

            // ── Super-Admin ───────────────────────────────────────────
            // Gets every single permission — including permanent-delete
            'Super-Admin' => '*',   // handled separately below

            // ── BOH-IT ────────────────────────────────────────────────
            // Full operational access; can manage users/roles/permissions
            // but cannot permanently delete anything (only Super-Admin can)
            'BOH-IT' => [
                'view-dashboard', 'view-boh-dashboard',
                // Users
                'list-users', 'view-users', 'create-users', 'edit-users', 'delete-users', 'restore-users',
                // Roles & Permissions (assign but not force-delete)
                'list-roles', 'view-roles', 'create-roles', 'edit-roles', 'delete-roles', 'restore-roles',
                'list-permissions', 'view-permissions', 'create-permissions', 'edit-permissions', 'assign-permissions',
                // Kids
                'list-kids', 'view-kids', 'create-kids', 'edit-kids', 'delete-kids', 'restore-kids',
                // Drivers
                'list-drivers', 'view-drivers', 'create-drivers', 'edit-drivers', 'delete-drivers', 'restore-drivers',
                // Parents
                'list-parents', 'view-parents', 'create-parents', 'edit-parents', 'delete-parents', 'restore-parents',
                // PickUp
                'list-pickup', 'view-pickup', 'create-pickup', 'edit-pickup', 'delete-pickup',
                // Location
                'list-country', 'view-country', 'create-country', 'edit-country', 'delete-country',
                'list-state',   'view-state',   'create-state',   'edit-state',   'delete-state',
                'list-city',    'view-city',    'create-city',    'edit-city',    'delete-city',
                // Subscriptions
                'list-plan', 'view-plan', 'create-plan', 'edit-plan', 'delete-plan',
                'list-subscription', 'view-subscription', 'create-subscription', 'edit-subscription', 'delete-subscription', 'unassign-subscription',
                // Rides
                'list-rideassign', 'view-rideassign', 'create-rideassign', 'edit-rideassign', 'delete-rideassign',
                'list-rides', 'view-rides',
                // BOH Operational
                'list-driver-shifts', 'view-driver-shifts', 'create-driver-shifts', 'edit-driver-shifts', 'delete-driver-shifts',
                'list-shift-broadcast', 'create-shift-broadcast', 'cancel-shift-broadcast',
                'list-timesheets', 'view-timesheets', 'approve-timesheets', 'reject-timesheets',
                'list-attendance', 'view-attendance',
                'list-driver-wages', 'view-driver-wages', 'create-driver-wages', 'edit-driver-wages', 'delete-driver-wages',
                'list-vehicle-types', 'view-vehicle-types', 'create-vehicle-types', 'edit-vehicle-types', 'delete-vehicle-types', 'assign-vehicle-types',
                'list-face-verification', 'view-face-verification',
                'view-trash', 'restore-trash',
            ],

            // ── BOH-Marketing ─────────────────────────────────────────
            // Sees subscription & plan data; cannot view child/parent PII
            'BOH-Marketing' => [
                'view-dashboard', 'view-boh-dashboard',
                // Plans & Subscriptions (full CRUD)
                'list-plan', 'view-plan', 'create-plan', 'edit-plan', 'delete-plan',
                'list-subscription', 'view-subscription', 'create-subscription', 'edit-subscription', 'delete-subscription', 'unassign-subscription',
                // Rides — read only (for reporting)
                'list-rides', 'view-rides',
                // Driver list — read only (for route/capacity planning)
                'list-drivers', 'view-drivers',
                // Vehicle Types — read only
                'list-vehicle-types', 'view-vehicle-types',
                // PickUp Types — read only
                'list-pickup', 'view-pickup',
            ],

            // ── BOH-Sales ─────────────────────────────────────────────
            // Manages parent/kid onboarding and subscription assignment
            'BOH-Sales' => [
                'view-dashboard', 'view-boh-dashboard',
                // Parents (full CRUD — onboarding)
                'list-parents', 'view-parents', 'create-parents', 'edit-parents', 'delete-parents',
                // Kids (full CRUD — onboarding)
                'list-kids', 'view-kids', 'create-kids', 'edit-kids', 'delete-kids',
                // Subscriptions (assign & manage)
                'list-plan', 'view-plan',
                'list-subscription', 'view-subscription', 'create-subscription', 'edit-subscription', 'unassign-subscription',
                // PickUp Types — read only
                'list-pickup', 'view-pickup',
                // Rides — read only
                'list-rideassign', 'view-rideassign',
                'list-rides', 'view-rides',
            ],

            // ── BOH-Support ───────────────────────────────────────────
            // Read-heavy role; resolves operational issues without editing sensitive data
            'BOH-Support' => [
                'view-dashboard', 'view-boh-dashboard',
                // Kids — read + edit (update contact info for support)
                'list-kids', 'view-kids', 'edit-kids',
                // Parents — read + edit
                'list-parents', 'view-parents', 'edit-parents',
                // Drivers — read only
                'list-drivers', 'view-drivers',
                // Rides & Assignments — read only
                'list-rideassign', 'view-rideassign',
                'list-rides', 'view-rides',
                // Subscriptions — read only
                'list-plan', 'view-plan',
                'list-subscription', 'view-subscription',
                // Timesheets — read only
                'list-timesheets', 'view-timesheets',
                // Attendance — read only
                'list-attendance', 'view-attendance',
                // Face Verification — read only
                'list-face-verification', 'view-face-verification',
                // PickUp — read only
                'list-pickup', 'view-pickup',
            ],
            'driver' => [],
            'parent' => [],
        ];

        // ─────────────────────────────────────────────────────────────
        // Create roles and assign permissions
        // ─────────────────────────────────────────────────────────────
        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );

            if ($perms === '*') {
                // Super-Admin gets every permission
                $all = Permission::all();
                $role->syncPermissions($all);
                $this->command->info("✅ Role [{$roleName}] → {$all->count()} permissions (all).");
            } else {
                // Resolve only permissions that actually exist in DB
                $existing = Permission::whereIn('name', $perms)->get();
                $role->syncPermissions($existing);
                $this->command->info("✅ Role [{$roleName}] → {$existing->count()} permissions.");

                // Warn about any permission names that don't exist yet
                $missing = array_diff($perms, $existing->pluck('name')->toArray());
                if (!empty($missing)) {
                    $this->command->warn("   ⚠ Missing permissions for [{$roleName}]: " . implode(', ', $missing));
                }
            }
        }
    }
}
