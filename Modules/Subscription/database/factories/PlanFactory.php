<?php

namespace Modules\Subscription\Database\Factories;

use Illuminate\Support\Str;
use Modules\Subscription\app\Models\Plan;
use Modules\PickUpType\app\Models\PickupType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Faker কে বলুন যে এটি একটি ইউনিক নাম তৈরি করুক
        $name = $this->faker->unique()->words(2, true);

        return [
            'pickup_type_id' => PickupType::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->randomFloat(2, 20, 200),
            'sell_price' => $this->faker->randomFloat(2, 15, 180),
            'currency' => 'AUD',
            'interval' => $this->faker->randomElement(['month', 'year']),
            'interval_count' => $this->faker->numberBetween(1, 12),
            'features' => [
                $this->faker->sentence,
                $this->faker->sentence,
                $this->faker->sentence,
            ],
            'status' => 'active',
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }
}
