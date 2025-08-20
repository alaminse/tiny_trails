<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Subscription\app\Models\Subscription;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition()
    {
        return [
            'name'          => $this->faker->word(),
            'price'         => $this->faker->randomFloat(2, 50, 500),
            'duration_days' => $this->faker->numberBetween(7, 365),
            'status'        => 'active',
        ];
    }
}

