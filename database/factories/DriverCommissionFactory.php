<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\DriverCommission\app\Models\DriverCommission;
use Modules\RideAssignment\app\Models\RideAssignment;

class DriverCommissionFactory extends Factory
{
    protected $model = DriverCommission::class;

    public function definition(): array
    {
        $baseFare = $this->faker->randomFloat(2, 10, 200);
        $commissionRate = $this->faker->randomFloat(2, 10, 25);
        $commissionAmount = ($baseFare * $commissionRate) / 100;
        $bonusAmount = $this->faker->boolean(30) ? $this->faker->randomFloat(2, 5, 50) : 0;
        $penaltyAmount = $this->faker->boolean(10) ? $this->faker->randomFloat(2, 5, 30) : 0;

        return [
            'driver_id' => User::factory()->driver(),
            'ride_assignment_id' => $this->faker->boolean(80) ? RideAssignment::factory() : null,
            'base_fare' => $baseFare,
            'commission_rate' => $commissionRate,
            'commission_amount' => round($commissionAmount, 2),
            'bonus_amount' => $bonusAmount,
            'penalty_amount' => $penaltyAmount,
            'total_earning' => round($commissionAmount + $bonusAmount - $penaltyAmount, 2),
            'commission_type' => $this->faker->randomElement([
                'per_ride', 'daily_bonus', 'weekly_bonus', 'monthly_bonus', 'referral_bonus', 'penalty'
            ]),
            'payment_status' => $this->faker->randomElement([
                'pending', 'processing', 'paid', 'failed'
            ]),
            'earning_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'payment_date' => $this->faker->boolean(60) ? $this->faker->dateTimeBetween('-3 months', 'now') : null,
            'payment_method' => $this->faker->optional(0.6)->randomElement(['bank_transfer', 'mobile_money', 'cash']),
            'payment_reference' => $this->faker->optional(0.6)->regexify('[A-Z0-9]{10}'),
            'bonus_type' => $bonusAmount > 0 ? $this->faker->randomElement(['completion_bonus', 'rating_bonus', 'referral_bonus']) : null,
            'penalty_type' => $penaltyAmount > 0 ? $this->faker->randomElement(['late_pickup', 'cancellation', 'low_rating']) : null,
            'description' => $this->faker->optional(0.3)->sentence(),
            'metadata' => $this->faker->optional(0.4)->randomElement([
                ['rating' => $this->faker->randomFloat(1, 3.5, 5.0)],
                ['completion_percentage' => $this->faker->numberBetween(80, 100)],
                ['distance_km' => $this->faker->numberBetween(5, 50)],
            ]),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'payment_date' => $this->faker->dateTimeBetween($attributes['earning_date'], 'now'),
            'payment_method' => $this->faker->randomElement(['bank_transfer', 'mobile_money']),
            'payment_reference' => $this->faker->regexify('[A-Z0-9]{10}'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'pending',
            'payment_date' => null,
            'payment_method' => null,
            'payment_reference' => null,
        ]);
    }

    public function bonus(): static
    {
        return $this->state(fn (array $attributes) => [
            'commission_type' => $this->faker->randomElement(['daily_bonus', 'weekly_bonus', 'monthly_bonus']),
            'bonus_amount' => $this->faker->randomFloat(2, 20, 100),
            'bonus_type' => $this->faker->randomElement(['completion_bonus', 'rating_bonus']),
            'base_fare' => 0,
            'commission_rate' => 0,
            'commission_amount' => 0,
        ]);
    }

    public function penalty(): static
    {
        return $this->state(fn (array $attributes) => [
            'commission_type' => 'penalty',
            'penalty_amount' => $this->faker->randomFloat(2, 10, 50),
            'penalty_type' => $this->faker->randomElement(['late_pickup', 'cancellation', 'low_rating']),
            'bonus_amount' => 0,
        ]);
    }
}