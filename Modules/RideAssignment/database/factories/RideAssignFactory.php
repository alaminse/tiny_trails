<?php

namespace Modules\RideAssignment\database\Factories;

use Modules\RideAssignment\app\Models\RideAssign;
use Modules\Subscription\app\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class RideAssignFactory extends Factory
{
    protected $model = RideAssign::class;

    public function definition()
    {
        $totalDays = $this->faker->numberBetween(1, 5);
        $selectedDates = [];
        for ($i = 0; $i < $totalDays; $i++) {
            $selectedDates[] = $this->faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d');
        }

        return [
            'subscription_id' => Subscription::factory(),
            'fare' => $this->faker->randomFloat(2, 10, 50),
            'driver_commission' => $this->faker->randomFloat(2, 5, 20),
            'platform_commission' => $this->faker->randomFloat(2, 5, 30),
            'service_type' => $this->faker->randomElement(['single_day', 'multiple_day']),
            'total_days' => $totalDays,
            'selected_dates' => json_encode($selectedDates),
            'status' => 'pending',
        ];
    }
}
