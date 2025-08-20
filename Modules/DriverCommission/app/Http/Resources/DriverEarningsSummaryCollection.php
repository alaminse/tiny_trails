<?php

namespace Modules\DriverCommission\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DriverEarningsSummaryCollection extends ResourceCollection
{
    public $collects = DriverEarningsSummaryResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total_count' => $this->resource->total(),
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
            ],
            'aggregates' => $this->when($request->has('include_aggregates'), function () {
                $summaries = $this->collection;
                
                return [
                    'total_rides' => $summaries->sum('total_rides'),
                    'total_completed_rides' => $summaries->sum('completed_rides'),
                    'total_earnings' => $summaries->sum('net_earnings'),
                    'average_completion_rate' => $summaries->avg('completion_rate'),
                    'average_rating' => $summaries->avg('average_rating'),
                    'total_distance' => $summaries->sum('total_distance_km'),
                    'total_duration' => $summaries->sum('total_duration_minutes'),
                ];
            }),
        ];
    }
}