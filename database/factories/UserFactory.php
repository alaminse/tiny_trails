<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'phone' => $this->faker->phoneNumber,
            'dob' => $this->faker->date,
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'address' => $this->faker->address,
            'country_id' => 1, // Assuming Australia's ID is 1
            'state_id' => $this->faker->numberBetween(1, 8), // Assuming 8 states
            'city_id' => $this->faker->numberBetween(1, 20), // Assuming 20 cities
            'fcm_token' => Str::random(60),
            'status' => 'active',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
}
