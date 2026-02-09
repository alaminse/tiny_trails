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

            // FIX: Handle the photo URL safely.
            // This assumes 'photo' is a string column in your database.
            'photo' => $this->photo ? getImageUrl($this->photo) : null,

            'distance_between_locations' => $this->distance_between_locations,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // FIX: Use the new LocationResource to safely load related location data.
            // This prevents errors if the relationships are not loaded.
            'pickup_location_details' => new LocationResource($this->whenLoaded('pickupLocation')),
            'dropoff_location_details' => new LocationResource($this->whenLoaded('dropoffLocation')),
        ];
    }
}
