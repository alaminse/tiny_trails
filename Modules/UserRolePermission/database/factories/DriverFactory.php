<?php

namespace Modules\UserRolePermission\database\Factories;

use App\Models\User;
use Modules\UserRolePermission\app\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'driving_license_number' => $this->faker->unique()->numerify('DL-########'),
            'driving_license_expiry' => $this->faker->dateTimeBetween('+1 year', '+5 years'),
            'driving_license_image' => 'license.jpg',
            'car_model' => $this->faker->word,
            'car_make' => $this->faker->company,
            'car_year' => $this->faker->year,
            'car_color' => $this->faker->colorName,
            'car_plate_number' => $this->faker->unique()->regexify('[A-Z]{3}-[0-9]{3}'),
            'car_image' => 'car.jpg',
            'face_embedding' => $this->faker->sha256,
            'face_image' => 'face.jpg',
            'is_verified' => 1,
            'device_token' => $this->faker->sha256,
            'status' => 'active',
        ];
    }
}
