<?php

namespace Modules\Subscription\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PlanCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'pickup_type_id' => $plan->pickup_type_id,
                    'pickup_type' => $plan->pickupType ? [
                        'id' => $plan->pickupType->id,
                        'name' => $plan->pickupType->name,
                    ] : null,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'price' => (float) $plan->price,
                    'sell_price' => (float) $plan->sell_price,
                    'display_price' => $plan->sell_price > 0 ? (float) $plan->sell_price : (float) $plan->price,
                    'currency' => $plan->currency,
                    'formatted_price' => $this->formatPrice($plan->price, $plan->currency),
                    'formatted_sell_price' => $plan->sell_price > 0 ? $this->formatPrice($plan->sell_price, $plan->currency) : null,
                    'formatted_display_price' => $this->formatPrice(
                        $plan->sell_price > 0 ? $plan->sell_price : $plan->price,
                        $plan->currency
                    ),
                    'interval' => $plan->interval,
                    'interval_count' => $plan->interval_count,
                    'billing_period' => $this->formatBillingPeriod($plan->interval_count, $plan->interval),
                    'features' => $plan->features ? json_decode($plan->features, true) : [],
                    'plan_tier' => $plan->plan_tier,
                    'iot_level' => $plan->iot_level,
                    'includes_hardware' => $plan->includes_hardware,
                    'hardware_price' => (float) $plan->hardware_price,
                    'formatted_hardware_price' => $plan->includes_hardware ? $this->formatPrice($plan->hardware_price, $plan->currency) : null,
                    'status' => $plan->status,
                    'is_active' => $plan->status === 'active',
                    'sort_order' => $plan->sort_order,
                    'created_at' => $plan->created_at?->toISOString(),
                    'updated_at' => $plan->updated_at?->toISOString(),
                ];
            }),
            'meta' => [
                'total' => $this->collection->count(),
                'active_count' => $this->collection->where('status', 'active')->count(),
                'inactive_count' => $this->collection->where('status', 'inactive')->count(),
                'plans_with_hardware' => $this->collection->where('includes_hardware', true)->count(),
                'plans_by_tier' => $this->collection->groupBy('plan_tier')->map->count(),
            ]
        ];
    }

    /**
     * Format price with currency symbol
     */
    private function formatPrice($price, $currency = 'AUD')
    {
        $symbols = [
            'AUD' => '$',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];

        $symbol = $symbols[$currency] ?? $currency . ' ';

        return $symbol . number_format($price, 2);
    }

    /**
     * Format billing period for display
     */
    private function formatBillingPeriod($count, $interval)
    {
        if ($count == 1) {
            return 'per ' . $interval;
        }

        $pluralIntervals = [
            'day' => 'days',
            'week' => 'weeks',
            'month' => 'months',
            'year' => 'years',
        ];

        $plural = $pluralIntervals[$interval] ?? $interval . 's';

        return 'every ' . $count . ' ' . $plural;
    }
}
