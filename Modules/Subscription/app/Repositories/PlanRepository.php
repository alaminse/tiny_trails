<?php

namespace Modules\Subscription\app\Repositories;

use Modules\Subscription\app\Models\Plan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanRepository
{
    protected $model;

    public function __construct(Plan $model)
    {
        $this->model = $model;
    }

    /**
     * Get all plans
     */
    public function all(array $relations = []): Collection
    {
        return $this->model->with($relations)->orderBy('sort_order')->get();
    }

    /**
     * Get active plans
     */
    public function active(array $relations = []): Collection
    {
        return $this->model->with($relations)->active()->orderBy('sort_order')->get();
    }

    /**
     * Get paginated plans
     */
    public function paginate(int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        return $this->model->with($relations)->orderBy('sort_order')->paginate($perPage);
    }

    /**
     * Find plan by ID
     */
    public function findById(int $id, array $relations = []): ?Plan
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Find plan by slug
     */
    public function findBySlug(string $slug, array $relations = []): ?Plan
    {
        return $this->model->with($relations)->where('slug', $slug)->first();
    }

    /**
     * Create new plan
     */
    public function create(array $data): Plan
    {
        return $this->model->create($data);
    }

    /**
     * Update plan
     */
    public function update(int $id, array $data): bool
    {
        $plan = $this->findById($id);
        if (!$plan) {
            return false;
        }
        return $plan->update($data);
    }

    /**
     * Delete plan (soft delete)
     */
    public function delete(int $id): bool
    {
        $plan = $this->findById($id);
        if (!$plan) {
            return false;
        }
        return $plan->delete();
    }

    /**
     * Restore soft deleted plan
     */
    public function restore(int $id): bool
    {
        $plan = $this->model->withTrashed()->find($id);
        if (!$plan) {
            return false;
        }
        return $plan->restore();
    }

    /**
     * Permanently delete plan
     */
    public function forceDelete(int $id): bool
    {
        $plan = $this->model->withTrashed()->find($id);
        if (!$plan) {
            return false;
        }
        return $plan->forceDelete();
    }

    /**
     * Get trashed plans
     */
    public function getTrashed(array $relations = []): Collection
    {
        return $this->model->onlyTrashed()->with($relations)->orderBy('sort_order')->get();
    }

    /**
     * Check if slug exists
     */
    public function slugExists(string $slug, int $excludeId = null): bool
    {
        $query = $this->model->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    /**
     * Get plans by pickup type
     */
    public function getByPickupType(int $pickupTypeId, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where('pickup_type_id', $pickupTypeId)
            ->active()
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Search plans
     */
    public function search(string $term, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%")
                      ->orWhere('slug', 'like', "%{$term}%");
            })
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get plans for AJAX data table
     */
    public function getDataTableData(bool $trashed = false): Collection
    {
        $query = $trashed ? $this->model->onlyTrashed() : $this->model->whereNull('deleted_at');
        
        return $query->with(['pickupType'])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(array $sortData): bool
    {
        foreach ($sortData as $item) {
            $this->model->where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }
        return true;
    }

    /**
     * Duplicate plan
     */
    public function duplicate(int $id, array $overrides = []): ?Plan
    {
        $plan = $this->findById($id);
        if (!$plan) {
            return null;
        }

        $newPlanData = $plan->toArray();
        unset($newPlanData['id'], $newPlanData['created_at'], $newPlanData['updated_at'], $newPlanData['deleted_at']);
        
        // Add suffix to name and slug
        $newPlanData['name'] = $newPlanData['name'] . ' (Copy)';
        $newPlanData['slug'] = $newPlanData['slug'] . '-copy';
        
        // Make slug unique
        $counter = 1;
        while ($this->slugExists($newPlanData['slug'])) {
            $newPlanData['slug'] = $newPlanData['slug'] . '-' . $counter;
            $counter++;
        }

        // Apply any overrides
        $newPlanData = array_merge($newPlanData, $overrides);

        return $this->create($newPlanData);
    }


    /**
     * Get plan statistics
     */
    public function getStats(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->active()->count(),
            'inactive' => $this->model->inactive()->count(),
            'monthly_plans' => $this->model->where('interval', 'month')->count(),
            'yearly_plans' => $this->model->where('interval', 'year')->count(),
            'free_plans' => $this->model->where('price', 0)->count(),
            'paid_plans' => $this->model->where('price', '>', 0)->count(),
        ];
    }

    /**
     * Get plan revenue statistics
     */
    public function getRevenueStats(): array
    {
        $activePlans = $this->model->active()->get();
        
        $monthlyPlansRevenue = $activePlans->where('interval', 'month')->sum('price');
        $yearlyPlansRevenue = $activePlans->where('interval', 'year')->sum('price');
        
        return [
            'monthly_plans_revenue' => $monthlyPlansRevenue,
            'yearly_plans_revenue' => $yearlyPlansRevenue,
            'total_plans_value' => $monthlyPlansRevenue + $yearlyPlansRevenue,
            'average_plan_price' => $activePlans->avg('price'),
            'highest_priced_plan' => $activePlans->max('price'),
            'lowest_priced_plan' => $activePlans->where('price', '>', 0)->min('price'),
        ];
    }

    /**
     * Get plan subscription statistics
     */
    public function getPlanSubscriptionStats(): array
    {
        $plans = $this->model->withCount(['subscriptions', 'activeSubscriptions'])->get();
        
        return [
            'most_popular_plan' => $plans->sortByDesc('active_subscriptions_count')->first(),
            'least_popular_plan' => $plans->sortBy('active_subscriptions_count')->first(),
            'total_subscriptions_across_plans' => $plans->sum('subscriptions_count'),
            'total_active_subscriptions_across_plans' => $plans->sum('active_subscriptions_count'),
        ];
    }
}