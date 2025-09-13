<?php

namespace Modules\Subscription\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Subscription\app\Http\Requests\PlanRequest;
use Modules\Subscription\app\Http\Resources\PlanResource;
use Modules\Subscription\app\Http\Resources\PlanCollection;
use Modules\Subscription\app\Http\Resources\PlanSelectResource;
use Modules\Subscription\app\Repositories\PlanRepository;
use Modules\PickUpType\app\Models\PickupType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\Subscription\app\Models\Plan;

class PlanController extends Controller
{
    protected $planRepository;

    public function __construct(PlanRepository $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {

        $stats = $this->planRepository->getStats();
        $revenueStats = $this->planRepository->getRevenueStats();
        $pickupTypes = PickupType::active()->get();
        return view('subscription::plan.index', compact('stats', 'revenueStats', 'pickupTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $pickupTypes = PickupType::active()->get();
        return view('subscription::plan.create', compact('pickupTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlanRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['slug'] = checkslug('plans');
            $plan = $this->planRepository->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Plan created successfully.',
                'data' => new PlanResource($plan->load('pickupType'))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create plan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $plan = $this->planRepository->findById($id, ['pickupType', 'subscriptions']);

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new PlanResource($plan)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch plan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): JsonResponse
    {
        try {
            $plan = $this->planRepository->findById($id, ['pickupType']);

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found.'
                ], 404);
            }

            return response()->json(new PlanResource($plan));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch plan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlanRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $updated = $this->planRepository->update($id, $data);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found.'
                ], 404);
            }

            $plan = $this->planRepository->findById($id, ['pickupType']);

            return response()->json([
                'success' => true,
                'message' => 'Plan updated successfully.',
                'data' => new PlanResource($plan)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update plan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->planRepository->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Plan moved to trash successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete plan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $restored = $this->planRepository->restore($id);

            if (!$restored) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found in trash.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Plan restored successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore plan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete(int $id): JsonResponse
    {
        try {
            $deleted = $this->planRepository->forceDelete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Plan permanently deleted.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete plan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get data for DataTable
     */
    public function getData(Request $request): JsonResponse
    {
        try {
            $trashed = $request->boolean('trashed', false);
            $plans = $this->planRepository->getDataTableData($trashed);

            $html = view('subscription::plan.row', compact('plans'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'data' => new PlanCollection($plans)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch plans.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get plans for select dropdown
     */
    public function getSelectData(Request $request): JsonResponse
    {
        try {
            $pickupTypeId = $request->get('pickup_type_id');

            if ($pickupTypeId) {
                $plans = $this->planRepository->getByPickupType($pickupTypeId);
            } else {
                $plans = $this->planRepository->active();
            }

            return response()->json([
                'success' => true,
                'data' => PlanSelectResource::collection($plans)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch plans.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicate plan
     */
    public function duplicate(int $id): JsonResponse
    {
        try {
            $duplicatedPlan = $this->planRepository->duplicate($id);

            if (!$duplicatedPlan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Plan duplicated successfully.',
                'data' => new PlanResource($duplicatedPlan->load('pickupType'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate plan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'sort_data' => 'required|array',
                'sort_data.*.id' => 'required|integer|exists:plans,id',
                'sort_data.*.sort_order' => 'required|integer|min:0',
            ]);

            $this->planRepository->updateSortOrder($request->sort_data);

            return response()->json([
                'success' => true,
                'message' => 'Sort order updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sort order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Get subscription statistics
     */

/**
 * Get plan statistics
 */
public function getStats(): JsonResponse
{
    try {
        $stats = $this->planRepository->getStats();
        $revenueStats = $this->planRepository->getRevenueStats();
        $subscriptionStats = $this->planRepository->getPlanSubscriptionStats();

        return response()->json([
            'success' => true,
            'data' => [
                'plan_stats' => $stats,
                'revenue_stats' => $revenueStats,
                'subscription_stats' => $subscriptionStats
            ]
        ]);



    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch plan statistics.',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Get plan revenue statistics only
 */
public function getRevenueStats(): JsonResponse
{
    try {
        $revenueStats = $this->planRepository->getRevenueStats();

        return response()->json([
            'success' => true,
            'data' => $revenueStats
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch revenue statistics.',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Get plan subscription statistics only
 */
public function getSubscriptionStats(): JsonResponse
{
    try {
        $subscriptionStats = $this->planRepository->getPlanSubscriptionStats();

        return response()->json([
            'success' => true,
            'data' => $subscriptionStats
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch subscription statistics.',
            'error' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Search plans
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $term = $request->get('term', '');
            $plans = $this->planRepository->search($term, ['pickupType']);

            return response()->json([
                'success' => true,
                'data' => new PlanCollection($plans)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search plans.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
