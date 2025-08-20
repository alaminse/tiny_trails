<?php

// Database/Seeders/ExistingRideCommissionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\DriverCommission\app\Models\DriverCommission;
use Modules\RideAssignment\app\Models\RideAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExistingRideCommissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating commissions for existing ride assignments...');

        try {
            // Get all existing ride assignments
            $rideAssignments = RideAssignment::with('driver')->get();

            if ($rideAssignments->isEmpty()) {
                $this->command->warn('No ride assignments found in database.');
                return;
            }

            $this->command->info("Found {$rideAssignments->count()} ride assignments");

            // Check which rides already have commissions
            $existingCommissions = DriverCommission::whereNotNull('ride_assignment_id')
                ->pluck('ride_assignment_id')
                ->toArray();

            $ridesWithoutCommissions = $rideAssignments->whereNotIn('id', $existingCommissions);

            $this->command->info("Creating commissions for {$ridesWithoutCommissions->count()} rides without commissions");

            DB::transaction(function () use ($ridesWithoutCommissions) {
                foreach ($ridesWithoutCommissions as $ride) {
                    $this->createCommissionForRide($ride);
                }
            });

            // Create some additional non-ride commissions (bonuses, penalties)
            $this->createAdditionalCommissions($rideAssignments);

            $this->displayStatistics();

        } catch (\Exception $e) {
            $this->command->error("Seeder failed: {$e->getMessage()}");
            throw $e;
        }
    }

    private function createCommissionForRide($ride): void
    {
        if (!$ride->driver) {
            $this->command->warn("Ride {$ride->id} has no driver assigned, skipping...");
            return;
        }

        // Use ride data for commission calculation
        $baseFare = $ride->ride_fare ?? rand(50, 800);
        $commissionRate = $this->getCommissionRate();
        $commissionAmount = ($baseFare * $commissionRate) / 100;
        
        // Small chance of bonus or penalty for ride
        $bonusAmount = rand(1, 100) <= 15 ? rand(10, 50) : 0; // 15% chance
        $penaltyAmount = rand(1, 100) <= 5 ? rand(10, 30) : 0; // 5% chance
        
        $totalEarning = $commissionAmount + $bonusAmount - $penaltyAmount;
        
        // Use ride date for earning date
        $earningDate = Carbon::parse($ride->ride_date);
        $paymentStatus = $this->getPaymentStatus($earningDate);
        $paymentData = $this->getPaymentData($paymentStatus, $earningDate);

        try {
            DriverCommission::create([
                'driver_id' => $ride->driver_id,
                'ride_assignment_id' => $ride->id,
                'base_fare' => round($baseFare, 2),
                'commission_rate' => round($commissionRate, 2),
                'commission_amount' => round($commissionAmount, 2),
                'bonus_amount' => round($bonusAmount, 2),
                'penalty_amount' => round($penaltyAmount, 2),
                'total_earning' => round($totalEarning, 2),
                'commission_type' => 'per_ride',
                'payment_status' => $paymentStatus,
                'earning_date' => $earningDate->toDateString(),
                'payment_date' => $paymentData['payment_date'],
                'payment_method' => $paymentData['payment_method'],
                'payment_reference' => $paymentData['payment_reference'],
                'bonus_type' => $bonusAmount > 0 ? $this->getBonusType() : null,
                'penalty_type' => $penaltyAmount > 0 ? $this->getPenaltyType() : null,
                'description' => $this->getRideDescription($ride),
                'metadata' => $this->getRideMetadata($ride),
                'created_at' => $earningDate,
                'updated_at' => now(),
            ]);

            $this->command->info("Created commission for ride {$ride->id} (Driver: {$ride->driver->first_name})");

        } catch (\Exception $e) {
            $this->command->error("Failed to create commission for ride {$ride->id}: {$e->getMessage()}");
        }
    }

    private function createAdditionalCommissions($rideAssignments): void
    {
        $this->command->info('Creating additional bonuses and penalties...');

        // Get unique drivers from rides
        $drivers = $rideAssignments->groupBy('driver_id');

        foreach ($drivers as $driverId => $driverRides) {
            $driver = $driverRides->first()->driver;
            if (!$driver) continue;

            // Create some bonus commissions for this driver
            $this->createBonusCommissions($driver, $driverRides);
            
            // Small chance of penalty
            if (rand(1, 100) <= 20) { // 20% chance
                $this->createPenaltyCommission($driver);
            }
        }
    }

    private function createBonusCommissions($driver, $driverRides): void
    {
        $latestRideDate = $driverRides->max('ride_date');
        $latestDate = Carbon::parse($latestRideDate);

        // Weekly bonus (if driver has multiple rides)
        if ($driverRides->count() >= 5) {
            $this->createBonusCommission($driver->id, 'weekly_bonus', $latestDate->subDays(rand(1, 7)));
        }

        // Monthly bonus (random chance)
        if (rand(1, 100) <= 30) { // 30% chance
            $this->createBonusCommission($driver->id, 'monthly_bonus', $latestDate->subDays(rand(1, 30)));
        }

        // Referral bonus (rare)
        if (rand(1, 100) <= 10) { // 10% chance
            $this->createBonusCommission($driver->id, 'referral_bonus', $latestDate->subDays(rand(1, 15)));
        }
    }

    private function createBonusCommission($driverId, $type, $earningDate): void
    {
        $bonusAmount = match($type) {
            'weekly_bonus' => rand(200, 800),
            'monthly_bonus' => rand(500, 2000),
            'referral_bonus' => rand(100, 500),
            default => rand(50, 300)
        };

        $paymentStatus = $this->getPaymentStatus($earningDate);
        $paymentData = $this->getPaymentData($paymentStatus, $earningDate);

        DriverCommission::create([
            'driver_id' => $driverId,
            'ride_assignment_id' => null,
            'base_fare' => 0,
            'commission_rate' => 0,
            'commission_amount' => 0,
            'bonus_amount' => $bonusAmount,
            'penalty_amount' => 0,
            'total_earning' => $bonusAmount,
            'commission_type' => $type,
            'payment_status' => $paymentStatus,
            'earning_date' => $earningDate->toDateString(),
            'payment_date' => $paymentData['payment_date'],
            'payment_method' => $paymentData['payment_method'],
            'payment_reference' => $paymentData['payment_reference'],
            'bonus_type' => $this->getBonusTypeForCommission($type),
            'penalty_type' => null,
            'description' => $this->getBonusDescription($type),
            'metadata' => $this->getBonusMetadata($type),
            'created_at' => $earningDate,
            'updated_at' => now(),
        ]);
    }

    private function createPenaltyCommission($driver): void
    {
        $penaltyAmount = rand(20, 200);
        $earningDate = Carbon::now()->subDays(rand(1, 30));
        
        $paymentStatus = 'pending'; // Penalties are usually pending
        
        DriverCommission::create([
            'driver_id' => $driver->id,
            'ride_assignment_id' => null,
            'base_fare' => 0,
            'commission_rate' => 0,
            'commission_amount' => 0,
            'bonus_amount' => 0,
            'penalty_amount' => $penaltyAmount,
            'total_earning' => -$penaltyAmount,
            'commission_type' => 'penalty',
            'payment_status' => $paymentStatus,
            'earning_date' => $earningDate->toDateString(),
            'payment_date' => null,
            'payment_method' => null,
            'payment_reference' => null,
            'bonus_type' => null,
            'penalty_type' => $this->getPenaltyType(),
            'description' => 'Service quality penalty',
            'metadata' => ['reason' => 'Customer complaint', 'severity' => 'medium'],
            'created_at' => $earningDate,
            'updated_at' => now(),
        ]);
    }

    private function getCommissionRate(): float
    {
        return rand(10, 25); // 10-25% commission rate
    }

    private function getPaymentStatus(Carbon $earningDate): string
    {
        $daysSinceEarning = $earningDate->diffInDays(now());
        
        if ($daysSinceEarning > 30) {
            return 'paid';
        } elseif ($daysSinceEarning > 7) {
            return rand(1, 100) <= 80 ? 'paid' : 'pending';
        } elseif ($daysSinceEarning > 3) {
            return rand(1, 100) <= 60 ? 'paid' : 'pending';
        } else {
            return rand(1, 100) <= 20 ? 'paid' : 'pending';
        }
    }

    private function getPaymentData(string $status, Carbon $earningDate): array
    {
        if ($status !== 'paid') {
            return [
                'payment_date' => null,
                'payment_method' => null,
                'payment_reference' => null
            ];
        }

        $paymentDate = $earningDate->copy()->addDays(rand(1, 15));
        $methods = ['bkash', 'nagad', 'rocket', 'bank_transfer'];
        $method = $methods[array_rand($methods)];
        
        return [
            'payment_date' => $paymentDate->toDateString(),
            'payment_method' => $method,
            'payment_reference' => $this->generatePaymentReference($method)
        ];
    }

    private function generatePaymentReference(string $method): string
    {
        return match($method) {
            'bkash' => 'BKH' . rand(100000000, 999999999),
            'nagad' => 'NGD' . rand(100000000, 999999999),
            'rocket' => 'RKT' . rand(100000000, 999999999),
            'bank_transfer' => 'TXN' . rand(100000000, 999999999),
            default => 'REF' . rand(100000000, 999999999)
        };
    }

    private function getBonusType(): string
    {
        $types = ['tip_bonus', 'rating_bonus', 'punctuality_bonus', 'customer_feedback'];
        return $types[array_rand($types)];
    }

    private function getPenaltyType(): string
    {
        $types = ['late_pickup', 'customer_complaint', 'route_deviation', 'vehicle_condition'];
        return $types[array_rand($types)];
    }

    private function getBonusTypeForCommission(string $commissionType): string
    {
        return match($commissionType) {
            'weekly_bonus' => 'weekly_performance',
            'monthly_bonus' => 'monthly_achievement',
            'referral_bonus' => 'driver_referral',
            default => 'general_bonus'
        };
    }

    private function getRideDescription($ride): string
    {
        $descriptions = [
            'Regular ride commission',
            'School pickup service',
            'Airport transfer',
            'City ride service'
        ];
        
        return $descriptions[array_rand($descriptions)];
    }

    private function getBonusDescription(string $type): string
    {
        return match($type) {
            'weekly_bonus' => 'Weekly performance bonus',
            'monthly_bonus' => 'Monthly achievement bonus',
            'referral_bonus' => 'New driver referral bonus',
            default => 'Performance bonus'
        };
    }

    private function getRideMetadata($ride): array
    {
        return [
            'pickup_location' => $ride->pickup_location ?? 'Unknown',
            'dropoff_location' => $ride->dropoff_location ?? 'Unknown',
            'ride_date' => $ride->ride_date,
            'pickup_time' => $ride->pickup_time ?? '00:00:00',
            'rating' => round(rand(35, 50) / 10, 1),
            'distance_km' => rand(3, 40),
            'duration_minutes' => rand(15, 90)
        ];
    }

    private function getBonusMetadata(string $type): array
    {
        return match($type) {
            'weekly_bonus' => [
                'rides_completed' => rand(15, 35),
                'completion_rate' => rand(85, 100),
                'average_rating' => round(rand(40, 50) / 10, 1)
            ],
            'monthly_bonus' => [
                'monthly_rides' => rand(60, 120),
                'monthly_earnings' => rand(8000, 20000),
                'customer_satisfaction' => rand(85, 100)
            ],
            'referral_bonus' => [
                'referred_driver' => 'New Driver #' . rand(100, 999),
                'referral_date' => now()->subDays(rand(1, 30))->toDateString()
            ],
            default => []
        };
    }

    private function displayStatistics(): void
    {
        $stats = [
            'total_commissions' => DriverCommission::count(),
            'ride_commissions' => DriverCommission::whereNotNull('ride_assignment_id')->count(),
            'bonus_commissions' => DriverCommission::whereIn('commission_type', ['weekly_bonus', 'monthly_bonus', 'referral_bonus'])->count(),
            'penalty_commissions' => DriverCommission::where('commission_type', 'penalty')->count(),
            'total_earnings' => DriverCommission::sum('total_earning'),
            'pending_count' => DriverCommission::where('payment_status', 'pending')->count(),
            'paid_count' => DriverCommission::where('payment_status', 'paid')->count(),
        ];

        $this->command->info('');
        $this->command->info('=== COMMISSION CREATION STATISTICS ===');
        $this->command->info("Total Commissions Created: {$stats['total_commissions']}");
        $this->command->info("- Ride Commissions: {$stats['ride_commissions']}");
        $this->command->info("- Bonus Commissions: {$stats['bonus_commissions']}");
        $this->command->info("- Penalty Commissions: {$stats['penalty_commissions']}");
        $this->command->info("Total Earnings: ৳" . number_format($stats['total_earnings'], 2));
        $this->command->info("Pending Payments: {$stats['pending_count']}");
        $this->command->info("Paid Payments: {$stats['paid_count']}");
        $this->command->info('');
        $this->command->info('Commission seeder completed successfully!');
    }
}