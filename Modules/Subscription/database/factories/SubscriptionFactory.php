<?php

namespace Modules\Subscription\database\Factories;

use App\Models\User;
use Modules\Subscription\app\Models\Plan;
use Modules\Subscription\app\Models\Location;
use Modules\UserRolePermission\app\Models\Kid;
use Modules\Subscription\app\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'kid_id' => Kid::factory(),
            'pickup_location_id' => Location::factory(),
            'dropoff_location_id' => Location::factory(),
            'name' => $this->faker->name . "'s Subscription",
            'payway_customer_id' => 'cus_' . $this->faker->unique()->sha256,
            'payway_subscription_id' => 'sub_' . $this->faker->unique()->sha256,
            'payway_status' => 'active',
            'trial_days' => 7,
            'trial_ends_at' => now()->addDays(7),
            'status' => 'active',
            // এই লাইনটিকে আপনার ডাটাবেসের ENUM মানের সাথে মেলান
            'assign_ride' => $this->faker->randomElement(['assigned', 'unassigned']),
            'card_brand' => $this->faker->creditCardType,
            'card_last_four' => $this->faker->numerify('####'),
            'card_expiration' => $this->faker->creditCardExpirationDateString,
        ];
    }
}
