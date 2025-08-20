<?php

// Database/Seeders/DriverEarningsSummarySeeder.php (Updated)
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\DriverCommission\app\Models\DriverCommission;
use Modules\DriverCommission\app\Models\DriverEarningsSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DriverEarningsSummarySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Driver Earnings Summary Seeder...');

        try {
            $drivers = User::role('driver')->get();

            if ($drivers->isEmpty()) {
                $this->command->warn('No drivers found. Please run DriverCommissionSeeder first.');
                return;
            }

            // Check if we have commissions to summarize
            $totalCommissions = DriverCommission::count();
            if ($totalCommissions === 0) {
                $this->command->warn('No driver commissions found. Please run DriverCommissionSeeder first.');
                return;
            }

            $this->command->info("Found {$totalCommissions} commissions to summarize for {$drivers->count()} drivers");

            DB::transaction(function () use ($drivers) {
                foreach ($drivers as $driver) {
                    $this->createSummariesForDriver($driver);
                }
            });

            $this->displaySummaryStatistics();

        } catch (\Exception $e) {
            $this->command->error("Summary seeder failed: {$e->getMessage()}");
            throw $e;
        }
    }

    private function createSummariesForDriver(User $driver): void
    {
        $this->command->info("Creating summaries for: {$driver->first_name} {$driver->last_name}");

        // Check if driver has any commissions
        $driverCommissions = DriverCommission::where('driver_id', $driver->id)->count();
        if ($driverCommissions === 0) {
            $this->command->info("No commissions found for {$driver->first_name}, skipping...");
            return;
        }

        // Create daily summaries for last 90 days
        $this->createDailySummaries($driver);
        
        // Create weekly summaries for last 20 weeks
        $this->createWeeklySummaries($driver);
        
        // Create monthly summaries for last 12 months
        $this->createMonthlySummaries($driver);
    }

    private function createDailySummaries(User $driver): void
    {
        $summariesCreated = 0;
        
        for ($i = 0; $i < 90; $i++) {
            $date = Carbon::now()->subDays($i);
            
            $dailyCommissions = DriverCommission::where('driver_id', $driver->id)
                ->whereDate('earning_date', $date)
                ->get();

            if ($dailyCommissions->isEmpty()) continue;

            $this->createSummary($driver->id, $date, 'daily', $dailyCommissions);
            $summariesCreated++;
        }
        
        $this->command->info("Created {$summariesCreated} daily summaries for {$driver->first_name}");
    }

    private function createWeeklySummaries(User $driver): void
    {
        $summariesCreated = 0;
        
        for ($i = 0; $i < 20; $i++) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();
            
            $weeklyCommissions = DriverCommission::where('driver_id', $driver->id)
                ->whereBetween('earning_date', [$weekStart, $weekEnd])
                ->get();

            if ($weeklyCommissions->isEmpty()) continue;

            $this->createSummary($driver->id, $weekStart, 'weekly', $weeklyCommissions);
            $summariesCreated++;
        }
        
        $this->command->info("Created {$summariesCreated} weekly summaries for {$driver->first_name}");
    }

    private function createMonthlySummaries(User $driver): void
    {
        $summariesCreated = 0;
        
        for ($i = 0; $i < 12; $i++) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            
            $monthlyCommissions = DriverCommission::where('driver_id', $driver->id)
                ->whereBetween('earning_date', [$monthStart, $monthEnd])
                ->get();

            if ($monthlyCommissions->isEmpty()) continue;

            $this->createSummary($driver->id, $monthStart, 'monthly', $monthlyCommissions);
            $summariesCreated++;
        }
        
        $this->command->info("Created {$summariesCreated} monthly summaries for {$driver->first_name}");
    }

    private function createSummary(int $driverId, Carbon $date, string $type, $commissions): void
    {
        $rideCommissions = $commissions->where('commission_type', 'per_ride');
        $totalRides = $rideCommissions->count();
        $completedRides = $totalRides; // Assuming all rides in commission are completed
        $cancelledRides = $this->getCancelledRides($type);

        // Calculate total actual rides (completed + cancelled)
        $totalActualRides = $completedRides + $cancelledRides;
        
        $data = [
            'driver_id' => $driverId,
            'summary_date' => $date->toDateString(),
            'summary_type' => $type,
            'total_rides' => $totalActualRides,
            'completed_rides' => $completedRides,
            'cancelled_rides' => $cancelledRides,
            'total_fare' => round($commissions->sum('base_fare'), 2),
            'total_commission' => round($commissions->sum('commission_amount'), 2),
            'total_bonus' => round($commissions->sum('bonus_amount'), 2),
            'total_penalty' => round($commissions->sum('penalty_amount'), 2),
            'net_earnings' => round($commissions->sum('total_earning'), 2),
            'completion_rate' => $totalActualRides > 0 ? round(($completedRides / $totalActualRides) * 100, 2) : 100,
            'average_rating' => $this->getAverageRating($commissions),
            'total_distance_km' => $this->getTotalDistance($commissions, $type),
            'total_duration_minutes' => $this->getTotalDuration($commissions, $type),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        try {
            DriverEarningsSummary::updateOrCreate(
                [
                    'driver_id' => $driverId,
                    'summary_date' => $date->toDateString(),
                    'summary_type' => $type
                ],
                $data
            );
        } catch (\Exception $e) {
            $this->command->error("Failed to create {$type} summary for driver {$driverId} on {$date->toDateString()}: {$e->getMessage()}");
        }
    }

    private function getCancelledRides(string $type): int
    {
        return match($type) {
            'daily' => rand(0, 3),
            'weekly' => rand(2, 15),
            'monthly' => rand(5, 50),
            default => 0
        };
    }

    private function getAverageRating($commissions): float
    {
        $ratings = $commissions->map(function ($commission) {
            $metadata = $commission->metadata;
            if (is_array($metadata) && isset($metadata['rating'])) {
                return (float) $metadata['rating'];
            }
            return rand(35, 50) / 10; // Default rating between 3.5-5.0
        })->filter(function ($rating) {
            return $rating > 0;
        });

        return $ratings->isEmpty() ? 4.5 : round($ratings->avg(), 2);
    }

    private function getTotalDistance($commissions, string $type): float
    {
        $baseDistance = $commissions->map(function ($commission) {
            $metadata = $commission->metadata;
            if (is_array($metadata) && isset($metadata['distance_km'])) {
                return (float) $metadata['distance_km'];
            }
            return rand(3, 25); // Default distance
        })->sum();

        // Add some additional distance for non-commission activities
        $multiplier = match($type) {
            'daily' => 1.0,
            'weekly' => 1.15, // 15% additional non-commission rides
            'monthly' => 1.25, // 25% additional non-commission rides
            default => 1.0
        };

        return round($baseDistance * $multiplier, 2);
    }

    private function getTotalDuration($commissions, string $type): int
    {
        $baseDuration = $commissions->map(function ($commission) {
            $metadata = $commission->metadata;
            if (is_array($metadata) && isset($metadata['duration_minutes'])) {
                return (int) $metadata['duration_minutes'];
            }
            return rand(20, 90); // Default duration
        })->sum();

        // Add some additional duration for non-commission activities
        $multiplier = match($type) {
            'daily' => 1.0,
            'weekly' => 1.15,
            'monthly' => 1.25,
            default => 1.0
        };

        return intval($baseDuration * $multiplier);
    }

    private function displaySummaryStatistics(): void
    {
        $stats = [
            'total_summaries' => DriverEarningsSummary::count(),
            'daily_summaries' => DriverEarningsSummary::where('summary_type', 'daily')->count(),
            'weekly_summaries' => DriverEarningsSummary::where('summary_type', 'weekly')->count(),
            'monthly_summaries' => DriverEarningsSummary::where('summary_type', 'monthly')->count(),
            'total_net_earnings' => DriverEarningsSummary::sum('net_earnings'),
            'avg_completion_rate' => DriverEarningsSummary::avg('completion_rate'),
            'avg_rating' => DriverEarningsSummary::avg('average_rating'),
            'total_distance' => DriverEarningsSummary::sum('total_distance_km'),
            'total_duration_hours' => DriverEarningsSummary::sum('total_duration_minutes') / 60,
        ];

        $this->command->info('');
        $this->command->info('=== EARNINGS SUMMARY STATISTICS ===');
        $this->command->info("Total Summaries: {$stats['total_summaries']}");
        $this->command->info("- Daily Summaries: {$stats['daily_summaries']}");
        $this->command->info("- Weekly Summaries: {$stats['weekly_summaries']}");
        $this->command->info("- Monthly Summaries: {$stats['monthly_summaries']}");
        $this->command->info("Total Net Earnings: ৳" . number_format($stats['total_net_earnings'], 2));
        $this->command->info("Average Completion Rate: " . number_format($stats['avg_completion_rate'], 2) . "%");
        $this->command->info("Average Rating: " . number_format($stats['avg_rating'], 2) . "/5.0");
        $this->command->info("Total Distance: " . number_format($stats['total_distance'], 0) . " km");
        $this->command->info("Total Duration: " . number_format($stats['total_duration_hours'], 0) . " hours");
        $this->command->info('');
        $this->command->info('Driver Earnings Summary Seeder completed successfully!');
    }
}