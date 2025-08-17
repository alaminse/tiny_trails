<?php

namespace Modules\Subscription\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SubscriptionCollection extends ResourceCollection
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
                'active' => $this->collection->where('status', 'active')->count(),
                'inactive' => $this->collection->where('status', 'inactive')->count(),
                'on_trial' => $this->collection->filter(function ($subscription) {
                    return $subscription->isOnTrial();
                })->count(),
                'expired' => $this->collection->filter(function ($subscription) {
                    return $subscription->hasExpired();
                })->count(),
                'canceled' => $this->collection->filter(function ($subscription) {
                    return $subscription->isCanceled();
                })->count(),
            ],
        ];
    }
}