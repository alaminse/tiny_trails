<?php

// Database/Seeders/DriverCommissionSeeder.php (Fixed Version)
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\DriverCommission\app\Models\DriverCommission;
use Modules\RideAssignment\app\Models\RideAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DriverCommissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Driver Commission Seeder...');

        try {
            // Get or create drivers
            $drivers = User::role('driver')->where('status', 'active')->get();

            if ($drivers->isEmpty()) {
                $this->command->info('Creating sample drivers...');
                $drivers = $this->createSampleDrivers();
            }

            // Get existing ride assignments or create some
            $this->ensureRideAssignmentsExist($drivers);

            $this->command->info("Processing {$drivers->count()} drivers for commissions...");

            DB::transaction(function () use ($drivers) {
                foreach ($drivers as $driver) {
                    $this->createCommissionsForDriver($driver);
                }
            });

            $this->displayStatistics();

        } catch (\Exception $e) {
            $this->command->error("Seeder failed: {$e->getMessage()}");
            throw $e;
        }
    }

    private function createSampleDrivers()
    {
        $drivers = [
            ['Liam', 'Smith', 'liam.smith@driver.com', '01712345678'],
            ['Noah', 'Brown', 'noah.brown@driver.com', '01823456789'],
            ['Ethan', 'Wilson', 'ethan.wilson@driver.com', '01934567890'],
            ['Oliver', 'Taylor', 'oliver.taylor@driver.com', '01645678901'],
            ['Jack', 'Johnson', 'jack.johnson@driver.com', '01556789012'],
            ['Lucas', 'Lee', 'lucas.lee@driver.com', '01767890123'],
            ['Mason', 'Martin', 'mason.martin@driver.com', '01878901234'],
            ['Henry', 'Clark', 'henry.clark@driver.com', '01989012345']
        ];

        $driverCollection = collect();
        foreach ($drivers as $index => $driverData) {
            $driver = User::create([
                'first_name' => $driverData[0],
                'last_name' => $driverData[1],
                'email' => $driverData[2],
                'phone' => $driverData[3],
                'status' => 'active',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            
            $driver->assignRole('driver');
            $driverCollection->push($driver);
        }

        return $driverCollection;
    }

    private function ensureRideAssignmentsExist($drivers): void
    {
        // Check if RideAssignment table exists and has data
        $existingRides = 0;
        
        try {
            if (class_exists('Modules\RideAssignment\app\Models\RideAssignment')) {
                $existingRides = RideAssignment::count();
            }
        } catch (\Exception $e) {
            $this->command->warn('RideAssignment model not found, will create commissions without ride assignments');
        }

        if ($existingRides === 0) {
            $this->command->info('Creating sample ride assignments...');
            $this->createSampleRideAssignments($drivers);
        } else {
            $this->command->info("Found {$existingRides} existing ride assignments");
        }
    }

    private function createSampleRideAssignments($drivers): void
    {
        if (!class_exists('Modules\RideAssignment\app\Models\RideAssignment')) {
            $this->command->warn('RideAssignment model not available, skipping ride assignment creation');
            return;
        }

        // Get some parents for ride assignments
        $parents = User::role('parent')->take(10)->get();
        
        if ($parents->isEmpty()) {
            // Create a few parent users
            $parentData = [
                ['Sarah', 'Ahmed', 'sarah.ahmed@parent.com'],
                ['Maria', 'Khan', 'maria.khan@parent.com'],
                ['Lisa', 'Rahman', 'lisa.rahman@parent.com'],
            ];

            foreach ($parentData as $data) {
                $parent = User::create([
                    'first_name' => $data[0],
                    'last_name' => $data[1],
                    'email' => $data[2],
                    'phone' => '0181' . rand(1000000, 9999999),
                    'status' => 'active',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);
                $parent->assignRole('parent');
                $parents->push($parent);
            }
        }

        // Create ride assignments
        $locations = [
            'pickup' => ['Dhanmondi', 'Gulshan', 'Uttara', 'Banani', 'Wari', 'Mirpur', 'Mohammadpur'],
            'dropoff' => ['Airport', 'Sadarghat', 'New Market', 'Bashundhara', 'Panthapath', 'Farmgate']
        ];

        for ($i = 0; $i < 200; $i++) {
            try {
                RideAssignment::create([
                    'driver_id' => $drivers->random()->id,
                    'parent_id' => $parents->random()->id,
                    'kid_id' => null,
                    'ride_title' => 'Ride Assignment ' . ($i + 1),
                    'pickup_location' => $locations['pickup'][array_rand($locations['pickup'])],
                    'dropoff_location' => $locations['dropoff'][array_rand($locations['dropoff'])],
                    'ride_date' => Carbon::now()->subDays(rand(0, 180))->toDateString(),
                    'pickup_time' => sprintf('%02d:%02d:00', rand(6, 22), rand(0, 59)),
                    'ride_fare' => rand(50, 800),
                    'status' => ['assigned', 'accepted', 'in_progress', 'completed'][array_rand(['assigned', 'accepted', 'in_progress', 'completed'])],
                    'created_at' => Carbon::now()->subDays(rand(0, 180)),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                $this->command->warn("Could not create ride assignment: {$e->getMessage()}");
                break;
            }
        }

        $rideCount = RideAssignment::count();
        $this->command->info("Created {$rideCount} ride assignments");
    }

    private function createCommissionsForDriver(User $driver): void
    {
        $this->command->info("Creating commissions for: {$driver->first_name} {$driver->last_name}");

        // Get ride assignments for this driver
        $driverRides = collect();
        try {
            if (class_exists('Modules\RideAssignment\app\Models\RideAssignment')) {
                $driverRides = RideAssignment::where('driver_id', $driver->id)->get();
            }
        } catch (\Exception $e) {
            // Continue without ride assignments
        }

        // 1. Historical commissions (last 6 months)
        $this->createHistoricalCommissions($driver, $driverRides);
        
        // 2. Recent commissions (last 30 days)
        $this->createRecentCommissions($driver, $driverRides);
        
        // 3. Current month commissions
        $this->createCurrentMonthCommissions($driver, $driverRides);
    }

    private function createHistoricalCommissions(User $driver, $rides): void
    {
        for ($month = 6; $month >= 1; $month--) {
            $startDate = Carbon::now()->subMonths($month)->startOfMonth();
            $endDate = Carbon::now()->subMonths($month)->endOfMonth();
            
            $commissionCount = rand(15, 35);
            
            for ($i = 0; $i < $commissionCount; $i++) {
                $earningDate = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                );
                
                $this->createSingleCommission($driver->id, $earningDate, $rides);
            }
        }
    }

    private function createRecentCommissions(User $driver, $rides): void
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now()->subDay();
        
        $commissionCount = rand(20, 40);
        
        for ($i = 0; $i < $commissionCount; $i++) {
            $earningDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );
            
            $this->createSingleCommission($driver->id, $earningDate, $rides);
        }
    }

    private function createCurrentMonthCommissions(User $driver, $rides): void
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();
        
        $commissionCount = rand(10, 25);
        
        for ($i = 0; $i < $commissionCount; $i++) {
            $earningDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );
            
            $this->createSingleCommission($driver->id, $earningDate, $rides);
        }
    }

    private function createSingleCommission(int $driverId, Carbon $earningDate, $rides): void
    {
        $commissionType = $this->getRandomCommissionType();
        $baseFare = $this->getBaseFareByType($commissionType);
        $commissionRate = $this->getCommissionRateByType($commissionType);
        $commissionAmount = ($baseFare * $commissionRate) / 100;
        
        $bonusAmount = $this->getBonusAmount($commissionType);
        $penaltyAmount = $this->getPenaltyAmount($commissionType);
        
        $totalEarning = $commissionAmount + $bonusAmount - $penaltyAmount;
        
        $paymentStatus = $this->getPaymentStatus($earningDate);
        $paymentData = $this->getPaymentData($paymentStatus, $earningDate);

        // Get valid ride assignment ID
        $rideAssignmentId = null;
        if ($commissionType === 'per_ride' && $rides->isNotEmpty()) {
            // 80% chance to have a ride assignment for per_ride commissions
            if (rand(1, 100) <= 80) {
                $rideAssignmentId = $rides->random()->id;
            }
        }

        DriverCommission::create([
            'driver_id' => $driverId,
            'ride_assignment_id' => $rideAssignmentId,
            'base_fare' => round($baseFare, 2),
            'commission_rate' => round($commissionRate, 2),
            'commission_amount' => round($commissionAmount, 2),
            'bonus_amount' => round($bonusAmount, 2),
            'penalty_amount' => round($penaltyAmount, 2),
            'total_earning' => round($totalEarning, 2),
            'commission_type' => $commissionType,
            'payment_status' => $paymentStatus,
            'earning_date' => $earningDate->toDateString(),
            'payment_date' => $paymentData['payment_date'],
            'payment_method' => $paymentData['payment_method'],
            'payment_reference' => $paymentData['payment_reference'],
            'bonus_type' => $this->getBonusType($commissionType, $bonusAmount),
            'penalty_type' => $this->getPenaltyType($commissionType, $penaltyAmount),
            'description' => $this->getDescription($commissionType),
            'metadata' => $this->getMetadata($commissionType),
            'created_at' => $earningDate,
            'updated_at' => now(),
        ]);
    }

    // All the other private methods remain the same as in your original seeder
    private function getRandomCommissionType(): string
    {
        $types = [
            'per_ride' => 70,
            'daily_bonus' => 10,
            'weekly_bonus' => 8,
            'monthly_bonus' => 5,
            'referral_bonus' => 5,
            'penalty' => 2
        ];

        $rand = rand(1, 100);
        $cumulative = 0;
        
        foreach ($types as $type => $percentage) {
            $cumulative += $percentage;
            if ($rand <= $cumulative) {
                return $type;
            }
        }
        
        return 'per_ride';
    }

    private function getBaseFareByType(string $type): float
    {
        return match($type) {
            'per_ride' => rand(50, 800),
            'daily_bonus' => 0,
            'weekly_bonus' => 0,
            'monthly_bonus' => 0,
            'referral_bonus' => 0,
            'penalty' => rand(100, 500),
            default => rand(50, 800)
        };
    }

    private function getCommissionRateByType(string $type): float
    {
        return match($type) {
            'per_ride' => rand(10, 25),
            'daily_bonus' => 0,
            'weekly_bonus' => 0,
            'monthly_bonus' => 0,
            'referral_bonus' => 0,
            'penalty' => rand(5, 15),
            default => rand(10, 25)
        };
    }

    private function getBonusAmount(string $type): float
    {
        return match($type) {
            'daily_bonus' => rand(50, 300),
            'weekly_bonus' => rand(200, 800),
            'monthly_bonus' => rand(500, 2000),
            'referral_bonus' => rand(100, 500),
            'per_ride' => rand(1, 100) <= 20 ? rand(10, 50) : 0,
            default => 0
        };
    }

    private function getPenaltyAmount(string $type): float
    {
        return match($type) {
            'penalty' => rand(20, 200),
            'per_ride' => rand(1, 100) <= 5 ? rand(10, 50) : 0,
            default => 0
        };
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

        $paymentDate = $earningDate->copy()->addDays(rand(1, 30));
        $methods = ['bkash', 'nagad', 'rocket', 'bank_transfer', 'cash'];
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
            'cash' => 'CSH' . rand(10000, 99999),
            default => 'REF' . rand(100000000, 999999999)
        };
    }

    private function getBonusType(string $commissionType, float $bonusAmount): ?string
    {
        if ($bonusAmount <= 0) return null;

        return match($commissionType) {
            'daily_bonus' => ['completion_bonus', 'rating_bonus', 'punctuality_bonus'][array_rand(['completion_bonus', 'rating_bonus', 'punctuality_bonus'])],
            'weekly_bonus' => ['performance_bonus', 'consistency_bonus', 'target_achievement'][array_rand(['performance_bonus', 'consistency_bonus', 'target_achievement'])],
            'monthly_bonus' => ['monthly_target', 'excellence_bonus', 'loyalty_bonus'][array_rand(['monthly_target', 'excellence_bonus', 'loyalty_bonus'])],
            'referral_bonus' => 'driver_referral',
            'per_ride' => ['tip_bonus', 'rating_bonus'][array_rand(['tip_bonus', 'rating_bonus'])],
            default => null
        };
    }

    private function getPenaltyType(string $commissionType, float $penaltyAmount): ?string
    {
        if ($penaltyAmount <= 0) return null;

        $penaltyTypes = ['late_pickup', 'cancellation', 'low_rating', 'customer_complaint', 'route_deviation', 'vehicle_condition'];
        return $penaltyTypes[array_rand($penaltyTypes)];
    }

    private function getDescription(string $type): ?string
    {
        $descriptions = [
            'per_ride' => ['Regular ride commission', 'Airport pickup', 'Night ride', 'Express delivery'],
            'daily_bonus' => ['Daily completion bonus', 'High rating bonus', 'On-time performance'],
            'weekly_bonus' => ['Weekly target achieved', 'Consistent performance', 'Customer satisfaction'],
            'monthly_bonus' => ['Monthly excellence award', 'Top performer bonus', 'Loyalty reward'],
            'referral_bonus' => ['New driver referral', 'Successful onboarding bonus'],
            'penalty' => ['Late pickup penalty', 'Customer complaint', 'Route violation', 'Service quality issue']
        ];

        $typeDescriptions = $descriptions[$type] ?? ['General commission'];
        return rand(1, 100) <= 40 ? $typeDescriptions[array_rand($typeDescriptions)] : null;
    }

    private function getMetadata(string $type): ?array
    {
        if (rand(1, 100) > 40) return null;

        $baseMetadata = [
            'rating' => round(rand(35, 50) / 10, 1),
            'distance_km' => rand(2, 50),
            'duration_minutes' => rand(15, 120)
        ];

        return match($type) {
            'per_ride' => array_merge($baseMetadata, [
                'pickup_location' => ['Dhanmondi', 'Gulshan', 'Uttara', 'Banani', 'Wari'][array_rand(['Dhanmondi', 'Gulshan', 'Uttara', 'Banani', 'Wari'])],
                'dropoff_location' => ['Airport', 'Sadarghat', 'New Market', 'Bashundhara'][array_rand(['Airport', 'Sadarghat', 'New Market', 'Bashundhara'])]
            ]),
            'daily_bonus' => [
                'rides_completed' => rand(8, 15),
                'completion_rate' => rand(85, 100),
                'average_rating' => round(rand(40, 50) / 10, 1)
            ],
            'weekly_bonus' => [
                'weekly_rides' => rand(40, 80),
                'weekly_earnings' => rand(2000, 8000),
                'consistency_score' => rand(80, 100)
            ],
            default => $baseMetadata
        };
    }

    private function displayStatistics(): void
    {
        $stats = [
            'total_commissions' => DriverCommission::count(),
            'total_earnings' => DriverCommission::sum('total_earning'),
            'pending_count' => DriverCommission::where('payment_status', 'pending')->count(),
            'paid_count' => DriverCommission::where('payment_status', 'paid')->count(),
            'bonus_count' => DriverCommission::whereIn('commission_type', ['daily_bonus', 'weekly_bonus', 'monthly_bonus', 'referral_bonus'])->count(),
            'penalty_count' => DriverCommission::where('commission_type', 'penalty')->count(),
            'avg_commission' => DriverCommission::avg('commission_amount'),
            'with_rides' => DriverCommission::whereNotNull('ride_assignment_id')->count(),
        ];

        $this->command->info('');
        $this->command->info('=== COMMISSION STATISTICS ===');
        $this->command->info("Total Commissions: {$stats['total_commissions']}");
        $this->command->info("Total Earnings: ৳" . number_format($stats['total_earnings'], 2));
        $this->command->info("Pending Payments: {$stats['pending_count']}");
        $this->command->info("Paid Payments: {$stats['paid_count']}");
        $this->command->info("Bonus Commissions: {$stats['bonus_count']}");
        $this->command->info("Penalty Commissions: {$stats['penalty_count']}");
        $this->command->info("With Ride Assignments: {$stats['with_rides']}");
        $this->command->info("Average Commission: ৳" . number_format($stats['avg_commission'], 2));
        $this->command->info('');
        $this->command->info('Driver Commission Seeder completed successfully!');
    }
}