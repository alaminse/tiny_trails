<?php

namespace Modules\Subscription\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Subscription\app\Http\Resources\SubscriptionResource;
use Modules\Subscription\app\Http\Resources\SubscriptionCollection;
use Modules\Subscription\app\Repositories\SubscriptionRepository;
use Modules\Subscription\app\Repositories\PlanRepository;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\LocationManagement\app\Models\City;
use Modules\LocationManagement\app\Models\State;
use Modules\Subscription\app\Http\Requests\SubscriptionRequest;

class SubscriptionController extends Controller
{
    protected $subscriptionRepository;
    protected $planRepository;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        PlanRepository $planRepository
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->planRepository = $planRepository;
    }

    public function index()
    {
        $stats = $this->subscriptionRepository->getStats();
        $revenueStats = $this->subscriptionRepository->getRevenueStats();
        
        $users = User::role('parent')
                            ->active()
                            ->get();
        $plans = $this->planRepository->active();

        return view('subscription::subscription.index', compact('stats', 'revenueStats', 'users', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubscriptionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $subscription = $this->subscriptionRepository->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully.',
                'data' => new SubscriptionResource($subscription->load(['user', 'plan']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subscription.',
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
            $subscription = $this->subscriptionRepository->findById($id, ['user', 'plan']);
            
            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new SubscriptionResource($subscription)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription.',
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
            $subscription = $this->subscriptionRepository->findById($id, ['user', 'plan']);
            
            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found.'
                ], 404);
            }

            return response()->json(new SubscriptionResource($subscription));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubscriptionRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();

            $updated = $this->subscriptionRepository->update($id, $data);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found.'
                ], 404);
            }

            $subscription = $this->subscriptionRepository->findById($id, ['user', 'plan']);

            return response()->json([
                'success' => true,
                'message' => 'Subscription updated successfully.',
                'data' => new SubscriptionResource($subscription)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update subscription.',
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
            $deleted = $this->subscriptionRepository->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription moved to trash successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subscription.',
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
            $restored = $this->subscriptionRepository->restore($id);

            if (!$restored) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found in trash.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription restored successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore subscription.',
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
            $deleted = $this->subscriptionRepository->forceDelete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription permanently deleted.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete subscription.',
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
            $subscriptions = $this->subscriptionRepository->getDataTableData($trashed);

            $html = view('subscription::subscription.table-rows', compact('subscriptions'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'data' => new SubscriptionCollection($subscriptions)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscriptions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'reason' => 'nullable|string|max:500'
            ]);

            $canceled = $this->subscriptionRepository->cancel($id, $request->reason);

            if (!$canceled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription canceled successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reactivate subscription
     */
    public function reactivate(int $id): JsonResponse
    {
        try {
            $reactivated = $this->subscriptionRepository->reactivate($id);

            if (!$reactivated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription reactivated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reactivate subscription.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subscription statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = $this->subscriptionRepository->getStats();
            $revenueStats = $this->subscriptionRepository->getRevenueStats();

            return response()->json([
                'success' => true,
                'data' => [
                    'subscription_stats' => $stats,
                    'revenue_stats' => $revenueStats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search subscriptions
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $term = $request->get('term', '');
            $subscriptions = $this->subscriptionRepository->search($term, ['user', 'plan']);

            return response()->json([
                'success' => true,
                'data' => new SubscriptionCollection($subscriptions)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search subscriptions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user subscriptions
     */
    public function getUserSubscriptions(int $userId): JsonResponse
    {
        try {
            $subscriptions = $this->subscriptionRepository->getByUser($userId, ['plan']);

            return response()->json([
                'success' => true,
                'data' => new SubscriptionCollection($subscriptions)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user subscriptions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get plan subscriptions
     */
    public function getPlanSubscriptions(int $planId): JsonResponse
    {
        try {
            $subscriptions = $this->subscriptionRepository->getByPlan($planId, ['user']);

            return response()->json([
                'success' => true,
                'data' => new SubscriptionCollection($subscriptions)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch plan subscriptions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get states by country
     */
    public function stateGet(int $country): JsonResponse
    {
        try {
            $states = State::where('country_id', $country)
                          ->where('status', 'active')
                          ->orderBy('name')
                          ->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'data' => $states
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch states.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cities by state
     */
    public function cityGet(int $state): JsonResponse
    {
        try {
            $cities = City::where('state_id', $state)
                         ->where('status', 'active')
                         ->orderBy('name')
                         ->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'data' => $cities
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}