<?php

namespace Modules\Subscription\app\Repositories;

use Modules\Subscription\app\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class SubscriptionRepository
{
    protected $model;

    public function __construct(Subscription $model)
    {
        $this->model = $model;
    }

    /**
     * Get all subscriptions
     */
    public function all(array $relations = []): Collection
    {
        return $this->model->with($relations)->latest()->get();
    }

    /**
     * Get active subscriptions
     */
    public function active(array $relations = []): Collection
    {
        return $this->model->with($relations)->active()->latest()->get();
    }

    /**
     * Get paginated subscriptions
     */
    public function paginate(int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        return $this->model->with($relations)->latest()->paginate($perPage);
    }

    /**
     * Find subscription by ID
     */
    public function findById(int $id, array $relations = []): ?Subscription
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Find subscription by stripe ID
     */
    public function findByStripeId(string $stripeId, array $relations = []): ?Subscription
    {
        return $this->model->with($relations)->where('stripe_id', $stripeId)->first();
    }

    /**
     * Create new subscription
     */
    public function create(array $data): Subscription
    {
        return $this->model->create($data);
    }

    /**
     * Update subscription
     */
    public function update(int $id, array $data): bool
    {
        $subscription = $this->findById($id);
        if (!$subscription) {
            return false;
        }
        return $subscription->update($data);
    }

    /**
     * Delete subscription (soft delete)
     */
    public function delete(int $id): bool
    {
        $subscription = $this->findById($id);
        if (!$subscription) {
            return false;
        }
        return $subscription->delete();
    }

    /**
     * Restore soft deleted subscription
     */
    public function restore(int $id): bool
    {
        $subscription = $this->model->withTrashed()->find($id);
        if (!$subscription) {
            return false;
        }
        return $subscription->restore();
    }

    /**
     * Permanently delete subscription
     */
    public function forceDelete(int $id): bool
    {
        $subscription = $this->model->withTrashed()->find($id);
        if (!$subscription) {
            return false;
        }
        return $subscription->forceDelete();
    }

    /**
     * Get trashed subscriptions
     */
    public function getTrashed(array $relations = []): Collection
    {
        return $this->model->onlyTrashed()->with($relations)->latest()->get();
    }

    /**
     * Get user subscriptions
     */
    public function getByUser(int $userId, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    /**
     * Get plan subscriptions
     */
    public function getByPlan(int $planId, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('plan_id', $planId)
            ->latest()
            ->get();
    }

    /**
     * Get subscriptions on trial
     */
    public function getOnTrial(array $relations = []): Collection
    {
        return $this->model->with($relations)->onTrial()->latest()->get();
    }

    /**
     * Get expired subscriptions
     */
    public function getExpired(array $relations = []): Collection
    {
        return $this->model->with($relations)->expired()->latest()->get();
    }

    /**
     * Get canceled subscriptions
     */
    public function getCanceled(array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->whereNotNull('canceled_at')
            ->latest()
            ->get();
    }

    /**
     * Search subscriptions
     */
    public function search(string $term, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                      ->orWhere('stripe_id', 'like', "%{$term}%")
                      ->orWhere('stripe_status', 'like', "%{$term}%")
                      ->orWhereHas('user', function ($q) use ($term) {
                          $q->where('name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%");
                      })
                      ->orWhereHas('plan', function ($q) use ($term) {
                          $q->where('name', 'like', "%{$term}%");
                      });
            })
            ->latest()
            ->get();
    }

    /**
     * Get subscriptions for AJAX data table
     */
    public function getDataTableData(bool $trashed = false): Collection
    {
        $query = $trashed ? $this->model->onlyTrashed() : $this->model->whereNull('deleted_at');

        return $query->with(['user', 'plan'])
            ->latest()
            ->get();
    }

    /**
     * Cancel subscription
     */
    public function cancel(int $id, string $reason = null): bool
    {
        $subscription = $this->findById($id);
        if (!$subscription) {
            return false;
        }

        return $subscription->update([
            'canceled_at' => now(),
            'cancellation_reason' => $reason,
            'status' => 'inactive'
        ]);
    }

    /**
     * Reactivate subscription
     */
    public function reactivate(int $id): bool
    {
        $subscription = $this->findById($id);
        if (!$subscription) {
            return false;
        }

        return $subscription->update([
            'canceled_at' => null,
            'cancellation_reason' => null,
            'status' => 'active'
        ]);
    }

    /**
     * Get subscription statistics
     */
    public function getStats(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->active()->count(),
            'inactive' => $this->model->inactive()->count(),
            'on_trial' => $this->model->onTrial()->count(),
            'expired' => $this->model->expired()->count(),
            'canceled' => $this->model->whereNotNull('canceled_at')->count(),
        ];
    }

    /**
     * Get revenue statistics
     */
    public function getRevenueStats(): array
    {
        $activeSubscriptions = $this->model->with('plan')->active()->get();

        $monthlyRevenue = $activeSubscriptions->filter(function ($subscription) {
            return $subscription->plan && $subscription->plan->interval === 'month';
        })->sum(function ($subscription) {
            return $subscription->plan->price;
        });

        $yearlyRevenue = $activeSubscriptions->filter(function ($subscription) {
            return $subscription->plan && $subscription->plan->interval === 'year';
        })->sum(function ($subscription) {
            return $subscription->plan->price;
        });

        return [
            'monthly_revenue' => $monthlyRevenue,
            'yearly_revenue' => $yearlyRevenue,
            'total_active_value' => $monthlyRevenue + $yearlyRevenue,
        ];
    }

    public function getData(Request $request)
    {
        $query = Subscription::query();

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

        return $query->get();
    }

    public function findActiveByUserAndPlan($user_id, $plan_id)
    {
        return Subscription::where('user_id', $user_id)
                            ->where('plan_id', $plan_id)
                            ->where('stripe_status', 'active')
                            ->where('trial_ends_at', '>', now())
                            ->first();
    }
}
