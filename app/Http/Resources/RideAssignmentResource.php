<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class RideAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->ride_title,
            'pickup_location' => $this->pickup_location,
            'dropoff_location' => $this->dropoff_location,
            'pickup_coordinates' => [
                'latitude' => $this->pickup_latitude,
                'longitude' => $this->pickup_longitude,
            ],
            'dropoff_coordinates' => [
                'latitude' => $this->dropoff_latitude,
                'longitude' => $this->dropoff_longitude,
            ],
            'schedule' => [
                'date' => $this->ride_date,
                'pickup_time' => $this->pickup_time,
                'estimated_dropoff_time' => $this->estimated_dropoff_time,
                'is_recurring' => $this->is_recurring,
                'recurring_days' => $this->recurring_days,
                'recurring_end_date' => $this->recurring_end_date,
            ],
            'trip_details' => [
                'distance_km' => $this->distance_km,
                'estimated_duration_minutes' => $this->estimated_duration_minutes,
                'ride_type' => $this->ride_type,
                'special_instructions' => $this->special_instructions,
                'notes' => $this->notes,
            ],
            'financial' => [
                'ride_fare' => $this->ride_fare,
                'driver_commission' => $this->driver_commission,
                'platform_fee' => $this->platform_fee,
            ],
            'status_info' => [
                'status' => $this->status,
                'accepted_at' => $this->accepted_at,
                'started_at' => $this->started_at,
                'completed_at' => $this->completed_at,
                'cancelled_at' => $this->cancelled_at,
                'cancelled_by' => $this->cancelled_by,
                'cancellation_reason' => $this->cancellation_reason,
            ],
            'participants' => [
                'driver' => $this->whenLoaded('driver', function () {
                    return [
                        'id' => $this->driver->id,
                        'name' => $this->driver->first_name . ' ' . $this->driver->last_name,
                        'phone' => $this->driver->phone,
                        'status' => $this->driver->status,
                    ];
                }),
                'parent' => $this->whenLoaded('parent', function () {
                    return [
                        'id' => $this->parent->id,
                        'name' => $this->parent->first_name . ' ' . $this->parent->last_name,
                        'phone' => $this->parent->phone,
                    ];
                }),
                'kid' => $this->whenLoaded('kid', function () {
                    return [
                        'id' => $this->kid->id,
                        'name' => $this->kid->first_name . ' ' . $this->kid->last_name,
                        'age' => $this->kid->dob ? Carbon::parse($this->kid->dob)->age : null,
                    ];
                }),
            ],
            'timestamps' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
