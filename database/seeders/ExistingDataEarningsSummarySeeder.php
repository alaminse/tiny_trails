<?php

// Database/Seeders/ExistingDataEarningsSummarySeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\DriverCommission\app\Models\DriverCommission;
use Modules\DriverCommission\app\Models\DriverEarningsSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExistingDataEarningsSummarySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating earnings summaries from existing commission data...');

        try {
            // Check if we have commission data
            $totalCommissions = DriverCommission::count();
            if ($totalCommissions === 0) {
                $this->command->warn('No commission data found. Please run commission seeder first.');
                return;
            }

            $this->command->info("Found {$totalCommissions} existing commissions");

            // Get all drivers who have commissions
            $driversWithCommissions = DriverCommission::select('driver_id')
                ->distinct()
                ->with('driver')
                ->get()
                ->pluck('driver')
                ->filter();

            if ($driversWithCommissions->isEmpty()) {
                $this->command->warn('No drivers found with commissions.');
                return;
            }

            $this->command->info("Processing {$driversWithCommissions->count()} drivers");

            // Clear existing summaries to regenerate
            $this->command->info('Clearing existing summaries...');
            DriverEarningsSummary::truncate();

            DB::transaction(function () use ($driversWithCommissions) {
                foreach ($driversWithCommissions as $driver) {
                    $this->generateSummariesForDriver($driver);
                }
            });

            $this->displayFinalStatistics();

        } catch (\Exception $e) {
            $this->command->error("Summary generation failed: {$e->getMessage()}");
            throw $e;
        }
    }

    private function generateSummariesForDriver($driver): void
    {
        $this->command->info("Generating summaries for: {$driver->first_name} {$driver->last_name}");

        // Get all commissions for this driver
        $allCommissions = DriverCommission::where('driver_id', $driver->id)->get();
        
        if ($allCommissions->isEmpty()) {
            return;
        }

        // Get date range from actual data
        $minDate = Carbon::parse($allCommissions->min('earning_date'));
        $maxDate = Carbon::parse($allCommissions->max('earning_date'));

        $this->command->info("Commission date range: {$minDate->toDateString()} to {$maxDate->toDateString()}");

        // Generate daily summaries
        $dailySummaries = $this->generateDailySummaries($driver->id, $allCommissions, $minDate, $maxDate);
        
        // Generate weekly summaries
        $weeklySummaries = $this->generateWeeklySummaries($driver->id, $allCommissions, $minDate, $maxDate);
        
        // Generate monthly summaries
        $monthlySummaries = $this->generateMonthlySummaries($driver->id, $allCommissions, $minDate, $maxDate);

        $this->command->info("Created {$dailySummaries} daily, {$weeklySummaries} weekly, {$monthlySummaries} monthly summaries");
    }

    private function generateDailySummaries($driverId, $allCommissions, $minDate, $maxDate): int
    {
        $summariesCreated = 0;
        $currentDate = $minDate->copy();

        while ($currentDate <= $maxDate) {
            $dayCommissions = $allCommissions->filter(function ($commission) use ($currentDate) {
                return Carbon::parse($commission->earning_date)->isSameDay($currentDate);
            });

            if ($dayCommissions->isNotEmpty()) {
                $this->createSummaryRecord($driverId, $currentDate, 'daily', $dayCommissions);
                $summariesCreated++;
            }

            $currentDate->addDay();
        }

        return $summariesCreated;
    }

    private function generateWeeklySummaries($driverId, $allCommissions, $minDate, $maxDate): int
    {
        $summariesCreated = 0;
        $weeks = $this->getWeekRanges($minDate, $maxDate);

        foreach ($weeks as $week) {
            $weekCommissions = $allCommissions->filter(function ($commission) use ($week) {
                $commissionDate = Carbon::parse($commission->earning_date);
                return $commissionDate >= $week['start'] && $commissionDate <= $week['end'];
            });

            if ($weekCommissions->isNotEmpty()) {
                $this->createSummaryRecord($driverId, $week['start'], 'weekly', $weekCommissions);
                $summariesCreated++;
            }
        }

        return $summariesCreated;
    }

    private function generateMonthlySummaries($driverId, $allCommissions, $minDate, $maxDate): int
    {
        $summariesCreated = 0;
        $months = $this->getMonthRanges($minDate, $maxDate);

        foreach ($months as $month) {
            $monthCommissions = $allCommissions->filter(function ($commission) use ($month) {
                $commissionDate = Carbon::parse($commission->earning_date);
                return $commissionDate >= $month['start'] && $commissionDate <= $month['end'];
            });

            if ($monthCommissions->isNotEmpty()) {
                $this->createSummaryRecord($driverId, $month['start'], 'monthly', $monthCommissions);
                $summariesCreated++;
            }
        }

        return $summariesCreated;
    }

    private function createSummaryRecord($driverId, $date, $summaryType, $commissions): void
    {
        // Calculate ride statistics
        $rideCommissions = $commissions->where('commission_type', 'per_ride');
        $completedRides = $rideCommissions->count();
        
        // Estimate cancelled rides based on summary type
        $cancelledRides = $this->estimateCancelledRides($summaryType, $completedRides);
        $totalRides = $completedRides + $cancelledRides;

        // Calculate financial data
        $totalFare = $commissions->sum('base_fare');
        $totalCommission = $commissions->sum('commission_amount');
        $totalBonus = $commissions->sum('bonus_amount');
        $totalPenalty = $commissions->sum('penalty_amount');
        $netEarnings = $commissions->sum('total_earning');

        // Calculate performance metrics
        $completionRate = $totalRides > 0 ? round(($completedRides / $totalRides) * 100, 2) : 100;
        $averageRating = $this->calculateAverageRating($commissions);
        $totalDistance = $this->calculateTotalDistance($commissions, $summaryType);
        $totalDuration = $this->calculateTotalDuration($commissions, $summaryType);

        DriverEarningsSummary::create([
            'driver_id' => $driverId,
            'summary_date' => $date->toDateString(),
            'summary_type' => $summaryType,
            'total_rides' => $totalRides,
            'completed_rides' => $completedRides,
            'cancelled_rides' => $cancelledRides,
            'total_fare' => round($totalFare, 2),
            'total_commission' => round($totalCommission, 2),
            'total_bonus' => round($totalBonus, 2),
            'total_penalty' => round($totalPenalty, 2),
            'net_earnings' => round($netEarnings, 2),
            'completion_rate' => $completionRate,
            'average_rating' => $averageRating,
            'total_distance_km' => round($totalDistance, 2),
            'total_duration_minutes' => $totalDuration,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function getWeekRanges($minDate, $maxDate): array
    {
        $weeks = [];
        $current = $minDate->copy()->startOfWeek();

        while ($current <= $maxDate) {
            $weekEnd = $current->copy()->endOfWeek();
            if ($weekEnd > $maxDate) {
                $weekEnd = $maxDate->copy();
            }

            $weeks[] = [
                'start' => $current->copy(),
                'end' => $weekEnd
            ];

            $current->addWeek();
        }

        return $weeks;
    }

    private function getMonthRanges($minDate, $maxDate): array
    {
        $months = [];
        $current = $minDate->copy()->startOfMonth();

        while ($current <= $maxDate) {
            $monthEnd = $current->copy()->endOfMonth();
            if ($monthEnd > $maxDate) {
                $monthEnd = $maxDate->copy();
            }

            $months[] = [
                'start' => $current->copy(),
                'end' => $monthEnd
            ];

            $current->addMonth();
        }

        return $months;
    }

    private function estimateCancelledRides($summaryType, $completedRides): int
    {
        if ($completedRides === 0) return 0;

        // Estimate cancellation rate based on industry standards
        $cancellationRate = match($summaryType) {
            'daily' => 0.05,    // 5% daily cancellation
            'weekly' => 0.08,   // 8% weekly cancellation
            'monthly' => 0.10,  // 10% monthly cancellation
            default => 0.05
        };

        return max(0, intval($completedRides * $cancellationRate));
    }

    private function calculateAverageRating($commissions): float
    {
        $ratingsSum = 0;
        $ratingsCount = 0;

        foreach ($commissions as $commission) {
            if ($commission->metadata && is_array($commission->metadata)) {
                if (isset($commission->metadata['rating'])) {
                    $ratingsSum += floatval($commission->metadata['rating']);
                    $ratingsCount++;
                }
            }
        }

        if ($ratingsCount === 0) {
            // Default rating based on commission performance
            $penaltyCount = $commissions->where('commission_type', 'penalty')->count();
            $bonusCount = $commissions->whereIn('commission_type', ['daily_bonus', 'weekly_bonus', 'monthly_bonus'])->count();
            
            if ($penaltyCount > 0) {
                return round(rand(350, 420) / 100, 2); // 3.5-4.2 for drivers with penalties
            } elseif ($bonusCount > 0) {
                return round(rand(450, 500) / 100, 2); // 4.5-5.0 for drivers with bonuses
            } else {
                return round(rand(400, 470) / 100, 2); // 4.0-4.7 for regular drivers
            }
        }

        return round($ratingsSum / $ratingsCount, 2);
    }

    private function calculateTotalDistance($commissions, $summaryType): float
    {
        $totalDistance = 0;

        foreach ($commissions as $commission) {
            if ($commission->metadata && is_array($commission->metadata)) {
                if (isset($commission->metadata['distance_km'])) {
                    $totalDistance += floatval($commission->metadata['distance_km']);
                } else {
                    // Estimate distance based on commission type
                    $totalDistance += $this->estimateDistance($commission);
                }
            } else {
                $totalDistance += $this->estimateDistance($commission);
            }
        }

        // Add estimated non-commission rides
        $multiplier = match($summaryType) {
            'daily' => 1.0,
            'weekly' => 1.1,
            'monthly' => 1.15,
            default => 1.0
        };

        return $totalDistance * $multiplier;
    }

    private function calculateTotalDuration($commissions, $summaryType): int
    {
        $totalDuration = 0;

        foreach ($commissions as $commission) {
            if ($commission->metadata && is_array($commission->metadata)) {
                if (isset($commission->metadata['duration_minutes'])) {
                    $totalDuration += intval($commission->metadata['duration_minutes']);
                } else {
                    $totalDuration += $this->estimateDuration($commission);
                }
            } else {
                $totalDuration += $this->estimateDuration($commission);
            }
        }

        // Add estimated non-commission ride time
        $multiplier = match($summaryType) {
            'daily' => 1.0,
            'weekly' => 1.1,
            'monthly' => 1.15,
            default => 1.0
        };

        return intval($totalDuration * $multiplier);
    }

    private function estimateDistance($commission): float
    {
        // Estimate based on base fare (higher fare = longer distance)
        $baseFare = $commission->base_fare ?? 100;
        
        if ($baseFare <= 100) return rand(2, 8);      // Short rides
        if ($baseFare <= 300) return rand(8, 20);     // Medium rides
        if ($baseFare <= 600) return rand(20, 40);    // Long rides
        return rand(40, 60);                          // Very long rides
    }

    private function estimateDuration($commission): int
    {
        // Estimate based on base fare
        $baseFare = $commission->base_fare ?? 100;
        
        if ($baseFare <= 100) return rand(15, 30);    // Short rides
        if ($baseFare <= 300) return rand(30, 60);    // Medium rides  
        if ($baseFare <= 600) return rand(60, 90);    // Long rides
        return rand(90, 120);                         // Very long rides
    }

    private function displayFinalStatistics(): void
    {
        $stats = [
            'total_summaries' => DriverEarningsSummary::count(),
            'daily_summaries' => DriverEarningsSummary::where('summary_type', 'daily')->count(),
            'weekly_summaries' => DriverEarningsSummary::where('summary_type', 'weekly')->count(),
            'monthly_summaries' => DriverEarningsSummary::where('summary_type', 'monthly')->count(),
            'total_drivers' => DriverEarningsSummary::distinct('driver_id')->count(),
            'total_net_earnings' => DriverEarningsSummary::sum('net_earnings'),
            'total_completed_rides' => DriverEarningsSummary::sum('completed_rides'),
            'avg_completion_rate' => DriverEarningsSummary::avg('completion_rate'),
            'avg_rating' => DriverEarningsSummary::avg('average_rating'),
            'total_distance' => DriverEarningsSummary::sum('total_distance_km'),
        ];

        $this->command->info('');
        $this->command->info('=== EARNINGS SUMMARY GENERATION COMPLETE ===');
        $this->command->info("Total Summaries Created: {$stats['total_summaries']}");
        $this->command->info("- Daily Summaries: {$stats['daily_summaries']}");
        $this->command->info("- Weekly Summaries: {$stats['weekly_summaries']}");
        $this->command->info("- Monthly Summaries: {$stats['monthly_summaries']}");
        $this->command->info("Drivers Processed: {$stats['total_drivers']}");
        $this->command->info("Total Net Earnings: ৳" . number_format($stats['total_net_earnings'], 2));
        $this->command->info("Total Completed Rides: " . number_format($stats['total_completed_rides']));
        $this->command->info("Average Completion Rate: " . number_format($stats['avg_completion_rate'], 1) . "%");
        $this->command->info("Average Rating: " . number_format($stats['avg_rating'], 2) . "/5.0");
        $this->command->info("Total Distance: " . number_format($stats['total_distance'], 0) . " km");
        $this->command->info('');
        $this->command->info('Summary generation completed successfully!');
    }
}