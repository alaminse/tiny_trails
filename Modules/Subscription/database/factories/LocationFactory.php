<?php

namespace Modules\Subscription\Database\Factories;

use Modules\Subscription\app\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition()
    {
        return [
            'address' => $this->faker->address,
            'latitude' => $this->faker->latitude(-33.9, -28.0), // Australian latitudes
            'longitude' => $this->faker->longitude(113.0, 154.0), // Australian longitudes
            'street1' => $this->faker->streetAddress,
            'street2' => $this->faker->secondaryAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'postal_code' => $this->faker->postcode,
            'country_code' => 'AU',
            'type' => $this->faker->randomElement(['pickup', 'dropoff']),
        ];
    }
}
