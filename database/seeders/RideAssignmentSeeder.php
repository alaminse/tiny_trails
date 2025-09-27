<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Modules\UserRolePermission\app\Models\Kid;
use Modules\Subscription\app\Models\Subscription;
use Carbon\Carbon;
use Modules\RideAssignment\app\Models\RideAssign;

class RideAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with different roles
       $drivers = User::role('driver')
            ->where('status', 'active')
            ->with('driver') // Load driver relationship
            ->get();

        $parents = User::role('parent')
            ->where('status', 'active')
            ->get();
        $kids = Kid::all();
        $subscriptions = Subscription::all();

        if ($drivers->isEmpty() || $parents->isEmpty()) {
            $this->command->warn('No drivers or parents found. Please seed users first.');
            return;
        }

        $statuses = ['assigned', 'accepted', 'in_progress', 'completed', 'cancelled'];
        $rideTypes = ['one_time', 'daily', 'weekly', 'custom'];
        $recurringDays = [
            ['monday', 'wednesday', 'friday'],
            ['tuesday', 'thursday'],
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            ['saturday', 'sunday']
        ];

        // Sample locations in Bangladesh
        $locations = [
            [
                'pickup' => 'Dhanmondi 32, Dhaka',
                'dropoff' => 'Gulshan 1, Dhaka',
                'pickup_lat' => 23.7461,
                'pickup_lng' => 90.3742,
                'dropoff_lat' => 23.7811,
                'dropoff_lng' => 90.4156
            ],
            [
                'pickup' => 'Banani, Dhaka',
                'dropoff' => 'Uttara, Dhaka',
                'pickup_lat' => 23.7936,
                'pickup_lng' => 90.4066,
                'dropoff_lat' => 23.8759,
                'dropoff_lng' => 90.3795
            ],
            [
                'pickup' => 'Wari, Dhaka',
                'dropoff' => 'Mirpur 10, Dhaka',
                'pickup_lat' => 23.7196,
                'pickup_lng' => 90.4201,
                'dropoff_lat' => 23.8069,
                'dropoff_lng' => 90.3684
            ],
            [
                'pickup' => 'Chittagong Port, Chittagong',
                'dropoff' => 'Agrabad, Chittagong',
                'pickup_lat' => 22.3384,
                'pickup_lng' => 91.8317,
                'dropoff_lat' => 22.3311,
                'dropoff_lng' => 91.8159
            ],
            [
                'pickup' => 'Rajshahi University, Rajshahi',
                'dropoff' => 'New Market, Rajshahi',
                'pickup_lat' => 24.3745,
                'pickup_lng' => 88.6042,
                'dropoff_lat' => 24.3636,
                'dropoff_lng' => 88.6241
            ]
        ];

        $rideAssignments = [];

        for ($i = 1; $i <= 50; $i++) {
            $driver = $drivers->random();
            $parent = $parents->random();
            $location = $locations[array_rand($locations)];
            $status = $statuses[array_rand($statuses)];
            $rideType = $rideTypes[array_rand($rideTypes)];
            $isRecurring = $rideType !== 'one_time' && rand(0, 1);

            // Calculate dates
            $rideDate = Carbon::now()->addDays(rand(-30, 30));
            $pickupTime = Carbon::createFromTime(rand(6, 22), rand(0, 3) * 15);
            $estimatedDropoffTime = $pickupTime->copy()->addMinutes(rand(15, 90));

            // Calculate financial details
            $rideFare = rand(200, 2000);
            $driverCommission = $rideFare * 0.15; // 15% commission
            $platformFee = $rideFare - $driverCommission;

            // Set status timestamps based on status
            $acceptedAt = null;
            $startedAt = null;
            $completedAt = null;
            $cancelledAt = null;
            $cancelledBy = null;
            $cancellationReason = null;

            if (in_array($status, ['accepted', 'in_progress', 'completed'])) {
                $acceptedAt = $rideDate->copy()->subHours(rand(1, 24));
            }
            if (in_array($status, ['in_progress', 'completed'])) {
                $startedAt = $acceptedAt ? $acceptedAt->copy()->addMinutes(rand(5, 60)) : $rideDate->copy();
            }
            if ($status === 'completed') {
                $completedAt = $startedAt ? $startedAt->copy()->addMinutes(rand(15, 90)) : $rideDate->copy();
            }
            if ($status === 'cancelled') {
                $cancelledAt = $rideDate->copy()->subHours(rand(1, 48));
                $cancelledBy = [$driver->id, $parent->id, 1][rand(0, 2)]; // Random canceller
                $cancellationReason = [
                    'Driver unavailable',
                    'Parent cancelled',
                    'Weather conditions',
                    'Emergency situation',
                    'Vehicle breakdown'
                ][rand(0, 4)];
            }

            $rideAssignments[] = [
                'driver_id' => $driver->id,
                'parent_id' => $parent->id,
                'kid_id' => $kids->isNotEmpty() && rand(0, 1) ? $kids->random()->id : null,
                'subscription_id' => $subscriptions->isNotEmpty() && rand(0, 1) ? $subscriptions->random()->id : null,
                'ride_title' => 'Ride #' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'pickup_location' => $location['pickup'],
                'dropoff_location' => $location['dropoff'],
                'pickup_latitude' => $location['pickup_lat'],
                'pickup_longitude' => $location['pickup_lng'],
                'dropoff_latitude' => $location['dropoff_lat'],
                'dropoff_longitude' => $location['dropoff_lng'],
                'ride_date' => $rideDate->format('Y-m-d'),
                'pickup_time' => $pickupTime->format('H:i:s'),
                'estimated_dropoff_time' => $estimatedDropoffTime->format('H:i:s'),
                'recurring_days' => $isRecurring ? json_encode($recurringDays[array_rand($recurringDays)]) : null,
                'is_recurring' => $isRecurring,
                'recurring_end_date' => $isRecurring ? $rideDate->copy()->addDays(rand(30, 90))->format('Y-m-d') : null,
                'distance_km' => rand(5, 50),
                'estimated_duration_minutes' => rand(15, 90),
                'ride_fare' => $rideFare,
                'driver_commission' => $driverCommission,
                'platform_fee' => $platformFee,
                'status' => $status,
                'ride_type' => $rideType,
                'special_instructions' => rand(0, 1) ? $this->getRandomInstruction() : null,
                'notes' => rand(0, 1) ? $this->getRandomNote() : null,
                'cancellation_reason' => $cancellationReason,
                'accepted_at' => $acceptedAt,
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'cancelled_at' => $cancelledAt,
                'cancelled_by' => $cancelledBy,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in chunks for better performance
        collect($rideAssignments)->chunk(10)->each(function ($chunk) {
            RideAssign::insert($chunk->toArray());
        });

        $this->command->info('Successfully seeded 50 ride assignments.');
    }

    /**
     * Get random special instruction
     */
    private function getRandomInstruction(): string
    {
        $instructions = [
            'Please call before arrival',
            'Kid needs help with seatbelt',
            'Pickup from school main gate',
            'Drop at front entrance',
            'Kid has special dietary requirements',
            'Please be patient with the child',
            'Backup contact available if needed',
            'Kid may need bathroom break',
            'Pickup from daycare center',
            'Please confirm arrival via SMS'
        ];

        return $instructions[array_rand($instructions)];
    }

    /**
     * Get random note
     */
    private function getRandomNote(): string
    {
        $notes = [
            'Regular weekly pickup for school',
            'One-time appointment to doctor',
            'Birthday party pickup arrangement',
            'Regular daycare pickup service',
            'Emergency pickup due to illness',
            'Weekend family visit ride',
            'School sports event transportation',
            'Medical appointment transport',
            'Shopping trip with family',
            'Visit to grandparents house'
        ];

        return $notes[array_rand($notes)];
    }
}
