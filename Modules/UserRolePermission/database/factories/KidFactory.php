<?php

namespace Modules\UserRolePermission\database\Factories;

use App\Models\User;
use Modules\UserRolePermission\app\Models\Kid;
use Illuminate\Database\Eloquent\Factories\Factory;

class KidFactory extends Factory
{
    protected $model = Kid::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'dob' => $this->faker->dateTimeBetween('-10 years', '-5 years'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'height_cm' => $this->faker->numberBetween(100, 160),
            'weight_kg' => $this->faker->numberBetween(20, 60),
            'photo' => 'kid.jpg',
            'school_name' => $this->faker->company . ' School',
            'school_address' => $this->faker->address,
            'emergency_contact' => $this->faker->phoneNumber,
            'kit_imei' => $this->faker->unique()->imei,
        ];
    }
}
