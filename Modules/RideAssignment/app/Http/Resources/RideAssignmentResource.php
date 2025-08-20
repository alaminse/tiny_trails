<?php

namespace Modules\RideAssignment\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RideAssignmentResource extends JsonResource
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
            'ride_title' => $this->ride_title,
            'pickup_location' => $this->pickup_location,
            'dropoff_location' => $this->dropoff_location,
            'pickup_latitude' => $this->pickup_latitude,
            'pickup_longitude' => $this->pickup_longitude,
            'dropoff_latitude' => $this->dropoff_latitude,
            'dropoff_longitude' => $this->dropoff_longitude,
            'ride_date' => $this->ride_date?->format('Y-m-d'),
            'formatted_ride_date' => $this->formatted_ride_date,
            'pickup_time' => $this->pickup_time?->format('H:i'),
            'formatted_pickup_time' => $this->formatted_pickup_time,
            'estimated_dropoff_time' => $this->estimated_dropoff_time?->format('H:i'),
            'formatted_estimated_dropoff_time' => $this->formatted_estimated_dropoff_time,
            'distance_km' => $this->distance_km,
            'estimated_duration_minutes' => $this->estimated_duration_minutes,
            'duration_display' => $this->duration_display,
            'ride_fare' => $this->ride_fare,
            'formatted_ride_fare' => $this->formatted_ride_fare,
            'driver_commission' => $this->driver_commission,
            'formatted_driver_commission' => $this->formatted_driver_commission,
            'platform_fee' => $this->platform_fee,
            'status' => $this->status,
            'status_badge' => $this->status_badge,
            'ride_type' => $this->ride_type,
            'ride_type_display' => $this->ride_type_display,
            'is_recurring' => $this->is_recurring,
            'recurring_days' => $this->recurring_days,
            'recurring_days_display' => $this->recurring_days_display,
            'recurring_end_date' => $this->recurring_end_date?->format('Y-m-d'),
            'special_instructions' => $this->special_instructions,
            'notes' => $this->notes,
            'cancellation_reason' => $this->cancellation_reason,
            'accepted_at' => $this->accepted_at?->format('Y-m-d H:i:s'),
            'started_at' => $this->started_at?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),
            'cancelled_at' => $this->cancelled_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relationships
            'driver' => $this->whenLoaded('driver', function () {
                return [
                    'id' => $this->driver->id,
                    'name' => $this->driver->name,
                    'email' => $this->driver->email,
                    'phone' => $this->driver->phone,
                    'profile_picture' => $this->driver->profile_picture,
                ];
            }),
            
            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'name' => $this->parent->name,
                    'email' => $this->parent->email,
                    'phone' => $this->parent->phone,
                    'profile_picture' => $this->parent->profile_picture,
                ];
            }),
            
            'kid' => $this->whenLoaded('kid', function () {
                return [
                    'id' => $this->kid->id,
                    'first_name' => $this->kid->first_name,
                    'last_name' => $this->kid->last_name,
                    'full_name' => $this->kid->first_name . ' ' . $this->kid->last_name,
                    'age' => $this->kid->age,
                    'grade' => $this->kid->grade,
                ];
            }),
            
            'subscription' => $this->whenLoaded('subscription', function () {
                return [
                    'id' => $this->subscription->id,
                    'plan_name' => $this->subscription->plan->name ?? null,
                    'status' => $this->subscription->status,
                    'start_date' => $this->subscription->start_date?->format('Y-m-d'),
                    'end_date' => $this->subscription->end_date?->format('Y-m-d'),
                ];
            }),
            
            'cancelled_by' => $this->whenLoaded('cancelledBy', function () {
                return [
                    'id' => $this->cancelledBy->id,
                    'name' => $this->cancelledBy->name,
                    'email' => $this->cancelledBy->email,
                ];
            }),
            
            // Status flags
            'can_be_accepted' => $this->canBeAccepted(),
            'can_be_started' => $this->canBeStarted(),
            'can_be_completed' => $this->canBeCompleted(),
            'can_be_cancelled' => $this->canBeCancelled(),
            'is_active' => $this->isActive(),
            'is_completed' => $this->isCompleted(),
            'is_cancelled' => $this->isCancelled(),
            'is_recurring' => $this->isRecurring(),
            'is_past_due' => $this->isPastDue(),
            'is_today' => $this->isToday(),
            'is_tomorrow' => $this->isTomorrow(),
            'is_upcoming' => $this->isUpcoming(),
        ];
    }
}