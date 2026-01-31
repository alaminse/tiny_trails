<?php

namespace Modules\PickUpType\database\Factories;

use Modules\PickUpType\app\Models\PickupType;
use Illuminate\Database\Eloquent\Factories\Factory;// মডিউলের PickupType মডেল ব্যবহার করুন

class PickupTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PickupType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Standard', 'Express', 'Scheduled']),
            'amount' => $this->faker->randomFloat(2, 5.00, 25.00),
            'min_notice_minutes' => $this->faker->numberBetween(15, 120), // 15 মিনিট থেকে 2 ঘন্টা
            'requires_instant_notification' => $this->faker->boolean(20), // 20% সম্ভাবনা যে true হবে
            'status' => 'active',
        ];
    }
}
