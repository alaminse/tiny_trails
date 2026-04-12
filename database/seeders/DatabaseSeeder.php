<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\PermissionSeeder;
use Modules\PickUpType\database\seeders\PickupTypeSeeder;
use Modules\LocationManagement\database\seeders\CitySeeder;
use Modules\LocationManagement\database\seeders\StateSeeder;
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
            UserSeeder::class,
            VehicleTypeSeeder::class,
            TwilioPermissionSeeder::class,
            TwilioCredentialSeeder::class,
        ]);
        $this->command->info('Static data seeded.');
        $this->command->info('Database seeding completed successfully!');
    }
}

