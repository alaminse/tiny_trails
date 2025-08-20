<?php

namespace Modules\RideAssignment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Modules\UserRolePermission\app\Models\Kid;
use Modules\Subscription\app\Models\Subscription;
use Carbon\Carbon;
use Modules\RideAssignment\app\Models\RideAssignment;

class RideAssignmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = RideAssignment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $rideDate = $this->faker->dateTimeBetween('-30 days', '+30 days');
        $pickupTime = $this->faker->time('H:i:s', '22:00:00');
        $rideFare = $this->faker->randomFloat(2, 200, 2000);
        $driverCommission = $rideFare * 0.15; // 15% commission
        $platformFee = $rideFare - $driverCommission;

        return [
            'driver_id' => User::factory()->driver(),
            'parent_id' => User::factory()->parent(),
            'kid_id' => $this->faker->boolean(70) ? Kid::factory() : null,
            'subscription_id' => $this->faker->boolean(30) ? Subscription::factory() : null,
            'ride_title' => 'Ride #' . $this->faker->unique()->numberBetween(1000, 9999),
            'pickup_location' => $this->faker->address(),
            'dropoff_location' => $this->faker->address(),
            'pickup_latitude' => $this->faker->latitude(20, 26), // Bangladesh latitude range
            'pickup_longitude' => $this->faker->longitude(88, 93), // Bangladesh longitude range
            'dropoff_latitude' => $this->faker->latitude(20, 26),
            'dropoff_longitude' => $this->faker->longitude(88, 93),
            'ride_date' => $rideDate->format('Y-m-d'),
            'pickup_time' => $pickupTime,
            'estimated_dropoff_time' => Carbon::createFromTimeString($pickupTime)
                ->addMinutes($this->faker->numberBetween(15, 90))
                ->format('H:i:s'),
            'recurring_days' => $this->faker->boolean(30) ? 
                json_encode($this->faker->randomElements([
                    'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
                ], $this->faker->numberBetween(1, 5))) : null,
            'is_recurring' => $this->faker->boolean(30),
            'recurring_end_date' => $this->faker->boolean(30) ? 
                $this->faker->dateTimeBetween($rideDate, '+90 days')->format('Y-m-d') : null,
            'distance_km' => $this->faker->randomFloat(2, 1, 50),
            'estimated_duration_minutes' => $this->faker->numberBetween(15, 90),
            'ride_fare' => $rideFare,
            'driver_commission' => $driverCommission,
            'platform_fee' => $platformFee,
            'status' => $this->faker->randomElement([
                'assigned', 'accepted', 'in_progress', 'completed', 'cancelled', 'no_show'
            ]),
            'ride_type' => $this->faker->randomElement([
                'one_time', 'daily', 'weekly', 'custom'
            ]),
            'special_instructions' => $this->faker->boolean(40) ? 
                $this->faker->sentence() : null,
            'notes' => $this->faker->boolean(30) ? 
                $this->faker->paragraph(2) : null,
            'cancellation_reason' => null, // Will be set by state methods if needed
            'accepted_at' => null,
            'started_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
        ];
    }

    /**
     * Indicate that the ride assignment is assigned.
     */
    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'assigned',
            'accepted_at' => null,
            'started_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
            'cancellation_reason' => null,
        ]);
    }

    /**
     * Indicate that the ride assignment is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'accepted_at' => $this->faker->dateTimeBetween('-2 hours', 'now'),
            'started_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
            'cancellation_reason' => null,
        ]);
    }

    /**
     * Indicate that the ride assignment is in progress.
     */
    public function inProgress(): static
    {
        $acceptedAt = $this->faker->dateTimeBetween('-3 hours', '-1 hour');
        $startedAt = $this->faker->dateTimeBetween($acceptedAt, 'now');

        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'accepted_at' => $acceptedAt,
            'started_at' => $startedAt,
            'completed_at' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
            'cancellation_reason' => null,
        ]);
    }

    /**
     * Indicate that the ride assignment is completed.
     */
    public function completed(): static
    {
        $acceptedAt = $this->faker->dateTimeBetween('-5 hours', '-3 hours');
        $startedAt = $this->faker->dateTimeBetween($acceptedAt, '-2 hours');
        $completedAt = $this->faker->dateTimeBetween($startedAt, 'now');

        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'accepted_at' => $acceptedAt,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'cancelled_at' => null,
            'cancelled_by' => null,
            'cancellation_reason' => null,
        ]);
    }

    /**
     * Indicate that the ride assignment is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'accepted_at' => $this->faker->boolean(50) ? 
                $this->faker->dateTimeBetween('-2 hours', 'now') : null,
            'started_at' => null,
            'completed_at' => null,
            'cancelled_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'cancelled_by' => User::factory(),
            'cancellation_reason' => $this->faker->randomElement([
                'Driver unavailable',
                'Parent cancelled',
                'Weather conditions',
                'Emergency situation',
                'Vehicle breakdown',
                'Traffic congestion',
                'Kid is sick',
                'Change of plans'
            ]),
        ]);
    }

    /**
     * Indicate that the ride assignment is a no show.
     */
    public function noShow(): static
    {
        $acceptedAt = $this->faker->dateTimeBetween('-3 hours', '-1 hour');

        return $this->state(fn (array $attributes) => [
            'status' => 'no_show',
            'accepted_at' => $acceptedAt,
            'started_at' => null,
            'completed_at' => null,
            'cancelled_at' => $this->faker->dateTimeBetween($acceptedAt, 'now'),
            'cancelled_by' => null,
            'cancellation_reason' => 'No show',
        ]);
    }

    /**
     * Indicate that the ride assignment is recurring.
     */
    public function recurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
            'ride_type' => $this->faker->randomElement(['daily', 'weekly', 'custom']),
            'recurring_days' => json_encode($this->faker->randomElements([
                'monday', 'tuesday', 'wednesday', 'thursday', 'friday'
            ], $this->faker->numberBetween(2, 5))),
            'recurring_end_date' => $this->faker->dateTimeBetween('+30 days', '+90 days')->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the ride assignment is one time.
     */
    public function oneTime(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => false,
            'ride_type' => 'one_time',
            'recurring_days' => null,
            'recurring_end_date' => null,
        ]);
    }

    /**
     * Indicate that the ride assignment is for today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'ride_date' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the ride assignment is for tomorrow.
     */
    public function tomorrow(): static
    {
        return $this->state(fn (array $attributes) => [
            'ride_date' => now()->addDay()->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the ride assignment is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'ride_date' => $this->faker->dateTimeBetween('tomorrow', '+30 days')->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the ride assignment is past.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'ride_date' => $this->faker->dateTimeBetween('-30 days', 'yesterday')->format('Y-m-d'),
        ]);
    }

    /**
     * Set a specific driver for the ride assignment.
     */
    public function forDriver(User $driver): static
    {
        return $this->state(fn (array $attributes) => [
            'driver_id' => $driver->id,
        ]);
    }

    /**
     * Set a specific parent for the ride assignment.
     */
    public function forParent(User $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }

    /**
     * Set a high fare for the ride assignment.
     */
    public function highFare(): static
    {
        $rideFare = $this->faker->randomFloat(2, 1500, 3000);
        $driverCommission = $rideFare * 0.15;
        $platformFee = $rideFare - $driverCommission;

        return $this->state(fn (array $attributes) => [
            'ride_fare' => $rideFare,
            'driver_commission' => $driverCommission,
            'platform_fee' => $platformFee,
        ]);
    }

    /**
     * Set a low fare for the ride assignment.
     */
    public function lowFare(): static
    {
        $rideFare = $this->faker->randomFloat(2, 100, 500);
        $driverCommission = $rideFare * 0.15;
        $platformFee = $rideFare - $driverCommission;

        return $this->state(fn (array $attributes) => [
            'ride_fare' => $rideFare,
            'driver_commission' => $driverCommission,
            'platform_fee' => $platformFee,
        ]);
    }
}