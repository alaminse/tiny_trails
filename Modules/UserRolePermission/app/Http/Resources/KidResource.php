<?php

namespace Modules\UserRolePermission\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KidResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'height_cm' => $this->height_cm,
            'weight_kg' => $this->weight_kg,
            'school_name' => $this->school_name,
            'school_address' => $this->school_address,
            'pickup_location' => $this->pickup_location,
            'dropoff_location' => $this->dropoff_location,
            'hair_color' => $this->hair_color,
            'eye_color' => $this->eye_color,
            'birthmarks' => $this->birthmarks,
            'emergency_contacts' => $this->when(isset($this->emergency_contacts), is_string($this->emergency_contacts) ? json_decode($this->emergency_contacts, true) : $this->emergency_contacts),
            'photo' => $this->photo ? getImageUrl($this->photo) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'distance_between_locations' => $this->distance_between_locations,
            'parent' => new UserResource($this->whenLoaded('parent')),
            'pickup_location_details' => new LocationResource($this->whenLoaded('pickupLocation')),
            'dropoff_location_details' => new LocationResource($this->whenLoaded('dropoffLocation')),
            'has_pending_wage' => $this->kidWages()->where('status', 'pending')->exists(),
            'pending_wage' => $this->whenLoaded('pendingWage', function () {
                return $this->pendingWage ? [
                    'id' => $this->pendingWage->id,
                    'plan_id' => $this->pendingWage->plan_id,
                    'price' => (float) $this->pendingWage->price,
                    'sell_price' => (float) $this->pendingWage->sell_price,
                    'start_date' => $this->pendingWage->start_date,
                    'end_date' => $this->pendingWage->end_date,
                    'status' => $this->pendingWage->status,
                    'notes' => $this->pendingWage->notes,
                    'plan' => $this->pendingWage->plan ? [  // ← added
                        'id' => $this->pendingWage->plan->id,
                        'name' => $this->pendingWage->plan->name,
                        'billing_period' => $this->pendingWage->plan->billing_period,
                        'interval' => $this->pendingWage->plan->interval,
                        'interval_count' => $this->pendingWage->plan->interval_count,
                    ] : null,
                ] : null;
            }),
        ];
    }
}

