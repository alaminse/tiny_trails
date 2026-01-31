<?php

namespace Modules\RideAssignment\database\Factories;

use App\Models\User;
use Modules\RideAssignment\app\Models\Ride;
use Modules\Subscription\app\Models\Location;
use Modules\RideAssignment\app\Models\RideAssign;
use Modules\UserRolePermission\app\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

class RideFactory extends Factory
{
    protected $model = Ride::class;

    public function definition()
    {
        return [
            'ride_assign_id' => RideAssign::factory(),
            'driver_id' => Driver::factory(),
            'parent_id' => User::factory(), // Assuming parent is also a user
            'pickup_location_id' => Location::factory(),
            'dropoff_location_id' => Location::factory(),
            'ride_type' => $this->faker->randomElement(['pickup', 'return_home']),
            'commission' => $this->faker->randomFloat(2, 5, 20),
            'date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'pickup' => $this->faker->time('H:i'),
            'drop_off' => $this->faker->time('H:i', '+3 hours'),
            'end_at' => $this->faker->dateTimeBetween('+1 hour', '+4 hours'),
            'face_verification1' => 'verification1.jpg',
            'selfie' => 'selfie.jpg',
            'face_verification2' => 'verification2.jpg',
            'end_pic' => 'end_pic.jpg',
            'status' => $this->faker->randomElement(['assigned',
                'pending',
                'going_to_pickup',
                'arrived_at_pickup',
                'in_progress',
                'completed',
                'cancelled']),
        ];
    }
}
