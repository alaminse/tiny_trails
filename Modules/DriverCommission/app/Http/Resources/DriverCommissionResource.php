<?php

namespace Modules\DriverCommission\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverCommissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'driver_id' => $this->driver_id,
            'ride_assignment_id' => $this->ride_assignment_id,
            
            // Financial Details
            'base_fare' => $this->base_fare,
            'commission_rate' => $this->commission_rate,
            'commission_amount' => $this->commission_amount,
            'bonus_amount' => $this->bonus_amount,
            'penalty_amount' => $this->penalty_amount,
            'total_earning' => $this->total_earning,
            
            // Commission Details
            'commission_type' => $this->commission_type,
            'payment_status' => $this->payment_status,
            'earning_date' => $this->earning_date?->toDateString(),
            'payment_date' => $this->payment_date?->toDateString(),
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            
            // Bonus/Penalty Information
            'bonus_type' => $this->bonus_type,
            'penalty_type' => $this->penalty_type,
            'description' => $this->description,
            'metadata' => $this->metadata,
            
            // Formatted Dates
            'formatted_earning_date' => $this->formatted_earning_date,
            'formatted_payment_date' => $this->formatted_payment_date,
            
            // Status Helpers
            'is_paid' => $this->isPaid(),
            'is_pending' => $this->isPending(),
            'is_processing' => $this->isProcessing(),
            
            // Relationships
            'driver' => $this->whenLoaded('driver', function () {
                return [
                    'id' => $this->driver->id,
                    'name' => $this->driver->name,
                    'email' => $this->driver->email,
                    'phone' => $this->driver->phone ?? null,
                ];
            }),
            
            'ride_assignment' => $this->whenLoaded('rideAssignment', function () {
                return [
                    'id' => $this->rideAssignment->id,
                    'pickup_location' => $this->rideAssignment->pickup_location ?? null,
                    'dropoff_location' => $this->rideAssignment->dropoff_location ?? null,
                    'status' => $this->rideAssignment->status ?? null,
                ];
            }),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
