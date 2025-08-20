<?php

namespace Modules\DriverCommission\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\DriverCommission\app\Models\DriverCommission;

class DriverCommissionSeeder extends Seeder
{
    public function run(): void
    {
        try {
            // Get active drivers
            $drivers = User::role('driver')
                ->where('status', 'active')
                ->get();

            if ($drivers->isEmpty()) {
                $this->command->info('No active drivers found. Creating sample drivers...');
                
                // Create drivers with proper role assignment
                $drivers = collect();
                for ($i = 0; $i < 10; $i++) {
                    $driver = User::factory()->create([
                        'status' => 'active'
                    ]);
                    $driver->assignRole('driver');
                    $drivers->push($driver);
                }
            }

            $this->command->info("Processing {$drivers->count()} drivers...");

            foreach ($drivers as $driver) {
                $this->command->info("Creating commissions for: {$driver->name}");

                try {
                    // Create different types of commissions
                    $this->createCommissionsForDriver($driver);
                } catch (\Exception $e) {
                    $this->command->error("Error creating commissions for driver {$driver->id}: {$e->getMessage()}");
                    continue;
                }
            }

            $totalCommissions = DriverCommission::count();
            $this->command->info("✅ Successfully created {$totalCommissions} driver commissions!");

        } catch (\Exception $e) {
            $this->command->error("Seeder failed: {$e->getMessage()}");
            throw $e;
        }
    }

    private function createCommissionsForDriver(User $driver): void
    {
        // Regular ride commissions
        DriverCommission::factory()
            ->count(rand(20, 40))
            ->create(['driver_id' => $driver->id]);

        // Paid commissions
        DriverCommission::factory()
            ->paid()
            ->count(rand(10, 20))
            ->create(['driver_id' => $driver->id]);

        // Pending commissions
        DriverCommission::factory()
            ->pending()
            ->count(rand(3, 8))
            ->create(['driver_id' => $driver->id]);

        // Bonus commissions
        DriverCommission::factory()
            ->bonus()
            ->count(rand(1, 4))
            ->create(['driver_id' => $driver->id]);

        // Penalty commissions (rare)
        if (rand(1, 3) === 1) {
            DriverCommission::factory()
                ->penalty()
                ->count(1)
                ->create(['driver_id' => $driver->id]);
        }
    }
}