<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RideResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->ride_title,
            'date' => $this->ride_date,
            'pickup_time' => $this->pickup_time,
            'pickup_location' => $this->pickup_location,
            'dropoff_location' => $this->dropoff_location,
            'fare' => $this->ride_fare,
            'status' => $this->status,
            'completed_at' => $this->completed_at,
            'cancelled_at' => $this->cancelled_at,
            'cancellation_reason' => $this->cancellation_reason,
            'driver' => $this->driver ? [
                'name' => $this->driver->first_name . ' ' . $this->driver->last_name,
                'phone' => $this->driver->phone,
            ] : null,
            'kid' => $this->kid ? [
                'name' => $this->kid->first_name . ' ' . $this->kid->last_name,
            ] : null,
        ];
    }
}