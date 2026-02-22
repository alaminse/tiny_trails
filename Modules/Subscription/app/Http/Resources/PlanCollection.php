<?php

namespace Modules\Subscription\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PlanCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($plan) {
                return [
                    'id'             => $plan->id,
                    'pickup_type_id' => $plan->pickup_type_id,
                    'name'           => $plan->name,
                    'slug'           => $plan->slug,
                    'description'    => $plan->description,

                    // Pricing
                    'price'                   => (float) $plan->price,
                    'sell_price'              => (float) $plan->sell_price,
                    'display_price'           => $plan->sell_price > 0 ? (float) $plan->sell_price : (float) $plan->price,
                    'currency'                => 'AUD',
                    'formatted_price'         => $this->formatPrice($plan->price),
                    'formatted_sell_price'    => $plan->sell_price > 0
                                                    ? $this->formatPrice($plan->sell_price)
                                                    : null,
                    'formatted_display_price' => $this->formatPrice(
                                                    $plan->sell_price > 0 ? $plan->sell_price : $plan->price
                                                ),

                    // Billing
                    'interval'       => $plan->interval,
                    'interval_count' => $plan->interval_count,
                    'billing_period' => $this->formatBillingPeriod($plan->interval_count, $plan->interval),

                    // Features & Tier
                    'features' => $plan->features ?? [],
                    'plan_tier' => $plan->plan_tier,
                    'iot_level' => $plan->iot_level,

                    // Hardware
                    'includes_hardware'        => (bool) $plan->includes_hardware,

                    'hardware_price' => $plan->includes_hardware ? (float) ($plan->hardware_price ?? 0) : null,
                    'formatted_hardware_price' => $plan->includes_hardware
                        ? $this->formatPrice($plan->hardware_price ?? 0)
                        : null,

                    // Status
                    'status'     => $plan->status,
                    'is_active'  => $plan->status === 'active',
                    'sort_order' => $plan->sort_order,

                    // Pickup Type
                    'pickup_type' => $plan->pickupType ? [
                        'id'                            => $plan->pickupType->id,
                        'name'                          => $plan->pickupType->name,
                        'amount'                        => (float) $plan->pickupType->amount,
                        'min_notice_minutes'            => (int) $plan->pickupType->min_notice_minutes,
                        'requires_instant_notification' => (bool) $plan->pickupType->requires_instant_notification,
                        'status'                        => $plan->pickupType->status,
                    ] : null,
                ];
            }),

            'meta' => [
                'total'               => $this->collection->count(),
                'active_count'        => $this->collection->where('status', 'active')->count(),
                'inactive_count'      => $this->collection->where('status', 'inactive')->count(),
                'plans_with_hardware' => $this->collection->where('includes_hardware', true)->count(),
                'plans_by_tier'       => $this->collection->groupBy('plan_tier')->map->count(),
            ],
        ];
    }

    private function formatPrice($price): string
    {
        return '$' . number_format((float) $price, 2);
    }

    private function formatBillingPeriod($count, $interval): string
    {
        // Handle trip interval
        if ($interval === 'trip') {
            return $count == 1 ? 'per trip' : 'every ' . $count . ' trips';
        }

        if ($count == 1) {
            return 'per ' . $interval;
        }

        // Named periods
        $namedPeriods = [
            'month' => [
                3  => 'per quarter',
                6  => 'per half year',
                12 => 'per year',
            ],
            'week' => [
                2 => 'every fortnight',
            ],
        ];

        if (isset($namedPeriods[$interval][$count])) {
            return $namedPeriods[$interval][$count];
        }

        $pluralIntervals = [
            'day'   => 'days',
            'week'  => 'weeks',
            'month' => 'months',
            'year'  => 'years',
        ];

        $plural = $pluralIntervals[$interval] ?? $interval . 's';

        return 'every ' . $count . ' ' . $plural;
    }
}
