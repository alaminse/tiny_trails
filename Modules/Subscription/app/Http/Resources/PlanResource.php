<?php

namespace Modules\Subscription\app\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pickup_type_id' => $this->pickup_type_id,
            'pickup_type' => $this->whenLoaded('pickupType', function () {
                return [
                    'id' => $this->pickupType->id,
                    'name' => $this->pickupType->name,
                ];
            }),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'sell_price' => (float) $this->sell_price,
            'formatted_price' => $this->formatted_price,
            'formatted_sell_price' => $this->formatted_sell_price,
            'currency' => $this->currency,
            'interval' => $this->interval,
            'interval_count' => $this->interval_count,
            'interval_display' => $this->interval_display,
            'features' => $this->features,
            'features_string' => $this->features_string,
            'plan_tier' => $this->plan_tier,
            'iot_level' => $this->iot_level,
            'includes_hardware' => $this->includes_hardware,
            'hardware_price' => (float) $this->hardware_price,
            'formatted_hardware_price' => $this->formatted_hardware_price,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'subscriptions_count' => $this->whenCounted('subscriptions'),
            'active_subscriptions_count' => $this->when(
                isset($this->active_subscriptions_count),
                $this->active_subscriptions_count
            ),
            'iot_devices' => $this->whenLoaded('iotDevices', function () {
                return $this->iotDevices->map(function ($device) {
                    return [
                        'id' => $device->id,
                        'iot_device_id' => $device->iot_device_id,
                        'is_included' => $device->is_included,
                        'extra_price' => (float) $device->extra_price,
                    ];
                });
            }),
        ];
    }
}
