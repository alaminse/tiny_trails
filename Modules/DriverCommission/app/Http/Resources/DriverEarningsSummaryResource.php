<?php

namespace Modules\DriverCommission\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverEarningsSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'driver_id' => $this->driver_id,
            'summary_date' => $this->summary_date?->toDateString(),
            'summary_type' => $this->summary_type,
            
            // Ride Statistics
            'total_rides' => $this->total_rides,
            'completed_rides' => $this->completed_rides,
            'cancelled_rides' => $this->cancelled_rides,
            
            // Financial Summary
            'total_fare' => $this->total_fare,
            'total_commission' => $this->total_commission,
            'total_bonus' => $this->total_bonus,
            'total_penalty' => $this->total_penalty,
            'net_earnings' => $this->net_earnings,
            
            // Performance Metrics
            'completion_rate' => $this->completion_rate,
            'average_rating' => $this->average_rating,
            'total_distance_km' => $this->total_distance_km,
            'total_duration_minutes' => $this->total_duration_minutes,
            
            // Formatted Data
            'formatted_summary_date' => $this->formatted_summary_date,
            'formatted_total_distance' => $this->formatted_total_distance,
            'formatted_total_duration' => $this->formatted_total_duration,
            
            // Driver Information
            'driver' => $this->whenLoaded('driver', function () {
                return [
                    'id' => $this->driver->id,
                    'name' => $this->driver->name,
                    'email' => $this->driver->email,
                ];
            }),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}