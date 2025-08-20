<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverCommissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'earning_details' => [
                'earning_date' => $this->earning_date,
                'commission_type' => $this->commission_type,
                'base_fare' => $this->base_fare,
                'commission_rate' => $this->commission_rate,
                'commission_amount' => $this->commission_amount,
                'bonus_amount' => $this->bonus_amount,
                'penalty_amount' => $this->penalty_amount,
                'total_earning' => $this->total_earning,
            ],
            'payment_info' => [
                'payment_status' => $this->payment_status,
                'payment_date' => $this->payment_date,
                'payment_method' => $this->payment_method,
                'payment_reference' => $this->payment_reference,
            ],
            'additional_info' => [
                'bonus_type' => $this->bonus_type,
                'penalty_type' => $this->penalty_type,
                'description' => $this->description,
                'metadata' => $this->metadata,
            ],
            'ride_info' => $this->whenLoaded('rideAssignment', function () {
                return [
                    'id' => $this->rideAssignment->id,
                    'title' => $this->rideAssignment->ride_title,
                    'pickup_location' => $this->rideAssignment->pickup_location,
                    'dropoff_location' => $this->rideAssignment->dropoff_location,
                ];
            }),
        ];
    }
}