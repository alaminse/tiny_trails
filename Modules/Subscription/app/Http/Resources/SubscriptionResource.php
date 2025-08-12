<?php

namespace Modules\Subscription\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\UserRolePermission\app\Http\Resources\UserResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'user_id'             => $this->user_id,
            'plan_id'             => $this->plan_id,
            'name'                => $this->name,
            'stripe_id'           => $this->stripe_id,
            'stripe_status'       => $this->stripe_status,
            'trial_ends_at'       => $this->trial_ends_at,
            'ends_at'             => $this->ends_at,
            'canceled_at'         => $this->canceled_at,
            'cancellation_reason' => $this->cancellation_reason,
            'card_brand'          => $this->card_brand,
            'card_last_four'      => $this->card_last_four,
            'card_expiration'     => $this->card_expiration,

            // Include relationships if they have been loaded.
            'user' => new UserResource($this->whenLoaded('user')),
            'plan' => new PlanResource($this->whenLoaded('plan')),
        ];
    }
}
