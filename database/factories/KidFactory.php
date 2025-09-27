<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\UserRolePermission\app\Models\Kid;

class KidFactory extends Factory
{
    protected $model = Kid::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'dob' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            // add other fields here
        ];
    }
}
