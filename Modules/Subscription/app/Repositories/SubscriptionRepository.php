<?php

namespace Modules\Subscription\app\Repositories;

use Illuminate\Http\Request;
use Modules\Subscription\app\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionRepository
{
    /**
     * Get a paginated collection of subscriptions with optional filters.
     * This method eager loads the `user` and `plan` relationships.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getData(Request $request)
    {
        $query = Subscription::query()->with(['user', 'plan']);

        // Filter by user ID if provided
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Include soft-deleted records if requested
        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        // You can add more filters here, such as by status or plan
        if ($request->filled('status')) {
             $query->where('stripe_status', $request->status);
        }

        return $query->paginate(15);
    }

    /**
     * Create a new subscription.
     *
     * @param array $data
     * @return Subscription
     */
    public function store(array $data)
    {
        return Subscription::create($data);
    }
    
    /**
     * Find a subscription by its ID.
     *
     * @param int $id
     * @return Subscription|null
     */
    public function find(int $id): ?Subscription
    {
        return Subscription::find($id);
    }
    
    /**
     * Soft delete a subscription.
     *
     * @param int $id
     * @return bool|null
     */
    public function delete(int $id): ?bool
    {
        $subscription = $this->find($id);
        return $subscription ? $subscription->delete() : false;
    }
    
    /**
     * Restore a soft-deleted subscription.
     *
     * @param int $id
     * @return bool|null
     */
    public function restore(int $id): ?bool
    {
        $subscription = Subscription::onlyTrashed()->find($id);
        return $subscription ? $subscription->restore() : false;
    }
}
