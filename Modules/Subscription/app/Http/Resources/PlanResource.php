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
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'price'          => $this->price, // Stored in cents, e.g., 1000 for $10.00
            'currency'       => $this->currency,
            'interval'       => $this->interval,
            'interval_count' => $this->interval_count,
            'stripe_plan'    => $this->stripe_plan,
            'features'       => json_decode($this->features), // Decode the JSON string into an array
            'is_active'      => $this->is_active,
            'sort_order'     => $this->sort_order,
        ];
    }
}
