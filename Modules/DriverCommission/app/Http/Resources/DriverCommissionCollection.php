<?php

namespace Modules\DriverCommission\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DriverCommissionCollection extends ResourceCollection
{
    public $collects = DriverCommissionResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total_count' => $this->resource->total(),
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'from' => $this->resource->firstItem(),
                'to' => $this->resource->lastItem(),
            ],
            'summary' => $this->when($request->has('include_summary'), function () {
                $commissions = $this->collection;

                return [
                    'total_commissions' => $commissions->count(),
                    'total_earnings' => $commissions->sum('total_earning'),
                    'total_commission_amount' => $commissions->sum('commission_amount'),
                    'total_bonus' => $commissions->sum('bonus_amount'),
                    'total_penalty' => $commissions->sum('penalty_amount'),
                    'pending_count' => $commissions->where('payment_status', 'pending')->count(),
                    'paid_count' => $commissions->where('payment_status', 'paid')->count(),
                ];
            }),
        ];
    }

    public function with(Request $request): array
    {
        return [
            'links' => [
                'first' => $this->resource->url(1),
                'last' => $this->resource->url($this->resource->lastPage()),
                'prev' => $this->resource->previousPageUrl(),
                'next' => $this->resource->nextPageUrl(),
            ],
        ];
    }
}
