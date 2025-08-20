<?php

namespace Modules\RideAssignment\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RideAssignmentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'statistics' => $this->getStatistics(),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Ride assignments retrieved successfully',
        ];
    }

    /**
     * Get statistics from the collection
     */
    private function getStatistics(): array
    {
        $rides = $this->collection;
        
        return [
            'total_rides' => $rides->count(),
            'assigned_rides' => $rides->where('status', 'assigned')->count(),
            'accepted_rides' => $rides->where('status', 'accepted')->count(),
            'in_progress_rides' => $rides->where('status', 'in_progress')->count(),
            'completed_rides' => $rides->where('status', 'completed')->count(),
            'cancelled_rides' => $rides->where('status', 'cancelled')->count(),
            'no_show_rides' => $rides->where('status', 'no_show')->count(),
            'recurring_rides' => $rides->where('is_recurring', true)->count(),
            'one_time_rides' => $rides->where('is_recurring', false)->count(),
            'total_revenue' => $rides->where('status', 'completed')->sum('ride_fare'),
            'total_commission' => $rides->where('status', 'completed')->sum('driver_commission'),
            'total_distance' => $rides->where('status', 'completed')->sum('distance_km'),
            'average_fare' => $rides->where('status', 'completed')->avg('ride_fare') ?: 0,
            'average_distance' => $rides->where('status', 'completed')->avg('distance_km') ?: 0,
        ];
    }
}