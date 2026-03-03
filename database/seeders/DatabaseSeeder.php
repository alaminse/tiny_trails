<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\PermissionSeeder;
use Modules\Subscription\database\seeders\PlanSeeder;
use Modules\RideAssignment\database\seeders\RideSeeder;
use Modules\PickUpType\database\seeders\PickupTypeSeeder;
use Modules\UserRolePermission\database\seeders\KidSeeder;
use Modules\LocationManagement\database\seeders\CitySeeder;
use Modules\LocationManagement\database\seeders\StateSeeder;
use Modules\RideAssignment\database\seeders\RideAssignSeeder;
use Modules\Subscription\database\seeders\SubscriptionSeeder;
use Modules\UserRolePermission\database\seeders\DriverSeeder;
use Modules\LocationManagement\database\seeders\CountrySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting database seeding...');

        // Static/Lookup Data
        $this->command->info('Seeding static data...');
        $this->call([
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            PickupTypeSeeder::class,
            SubscriptionSeeder::class,
            // PlanSeeder::class,
            UserSeeder::class,
        ]);
        $this->command->info('Static data seeded.');
        $this->command->info('Database seeding completed successfully!');
    }
}

