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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\LocationManagement\app\Models\City;
use Modules\LocationManagement\app\Models\State;
use Modules\Subscription\app\Http\Requests\SubscriptionRequest;
use Modules\Subscription\app\Http\Resources\PlanCollection;
use Modules\Subscription\app\Models\Plan;
use Modules\Subscription\app\Models\Subscription;
use Modules\UserRolePermission\app\Models\Kid;

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


    public function plans()
    {
        try {
            $plans = Plan::with('pickupType') // Assuming you have this relationship
                        ->where('status', 'active')
                        ->orderBy('sort_order')
                        ->get();

            return response()->json([
                'success' => true,
                'message' => 'Active plan list retrieved successfully.',
                'data' => new PlanCollection($plans)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch plans.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new subscription.
     */
    public function create(): View
    {
        $users = User::role('parent')
                    ->active()
                    ->get();
        $plans = $this->planRepository->active();

        return view('subscription::subscription.create', compact('users', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(SubscriptionRequest $request): JsonResponse
    // {
    //     try {
    //         $data = $request->validated();

    //         $subscription = $this->subscriptionRepository->create($data);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Subscription created successfully.',
    //             'data' => new SubscriptionResource($subscription->load(['user', 'plan']))
    //         ], 201);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to create subscription.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function show(Subscription $subscription): JsonResponse
    {
        try {
            // Load relationships if needed
            $subscription->load(['user', 'plan']);

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

            // Get DataTable parameters
            $draw = $request->input('draw', 1);
            $start = $request->input('start', 0);
            $length = $request->input('length', 25);
            $searchValue = $request->input('search.value', '');
            $orderColumn = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'desc');

            // Define column mapping
            $columns = [
                0 => 'id',
                1 => 'user.name',
                2 => 'plan.name',
                3 => 'status',
                4 => 'payway_status',
                6 => 'ends_at',
                7 => 'payment_method',
                8 => 'actions'
            ];

            // Build query
            $query = $trashed
                ? Subscription::onlyTrashed()
                : Subscription::whereNull('deleted_at');

            $query->with(['user', 'plan']);

            // Apply search
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('status', 'like', "%{$searchValue}%")
                    ->orWhere('payway_status', 'like', "%{$searchValue}%")
                    ->orWhereHas('user', function($q) use ($searchValue) {
                        $q->where('name', 'like', "%{$searchValue}%")
                            ->orWhere('email', 'like', "%{$searchValue}%");
                    })
                    ->orWhereHas('plan', function($q) use ($searchValue) {
                        $q->where('name', 'like', "%{$searchValue}%");
                    });
                });
            }

            // Get total count before pagination
            $totalRecords = Subscription::count();
            $filteredRecords = $query->count();

            // Apply ordering
            $orderColumnName = $columns[$orderColumn] ?? 'id';
            if (str_contains($orderColumnName, '.')) {
                $parts = explode('.', $orderColumnName);
                $query->orderBy($parts[0] . 's.' . $parts[1], $orderDirection);
            } else {
                $query->orderBy($orderColumnName, $orderDirection);
            }

            // Apply pagination
            $subscriptions = $query->skip($start)->take($length)->get();

            // Format data for DataTable
            $data = [];
            foreach ($subscriptions as $index => $subscription) {
                $data[] = [
                    'DT_RowIndex' => $start + $index + 1,
                    'id' => $subscription->id,
                    'user' => [
                        'name' => $subscription->user->name ?? 'N/A',
                        'email' => $subscription->user->email ?? 'N/A'
                    ],
                    'plan' => [
                        'name' => $subscription->plan->name ?? 'N/A',
                        'price' => $subscription->plan->price ?? 0
                    ],
                    'name' => $subscription->name,
                    'status' => ucfirst($subscription->status),
                    'payway_status' => ucfirst($subscription->payway_status ?? 'N/A'),
                    'ends_at' => $subscription->ends_at
                        ? $subscription->ends_at->format('Y-m-d H:i:s')
                        : 'N/A',
                    'payment_method' => $this->formatPaymentMethod($subscription),
                    'actions' => $this->generateActionButtons($subscription, $trashed),
                    'created_at' => $subscription->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $subscription->updated_at->format('Y-m-d H:i:s'),
                ];
            }

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('DataTable error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to fetch subscriptions: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Format payment method display
     */
    private function formatPaymentMethod($subscription)
    {
        if ($subscription->card_brand && $subscription->card_last_four) {
            return ucfirst($subscription->card_brand) . ' •••• ' . $subscription->card_last_four;
        }

        return 'N/A';
    }



    /**
     * Alternative simple getData method (if the above is too complex)
     */
    public function getDataSimple(Request $request): JsonResponse
    {
        try {
            $trashed = $request->boolean('trashed', false);

            $query = $trashed
                ? Subscription::onlyTrashed()
                : Subscription::whereNull('deleted_at');

            $subscriptions = $query->with(['user', 'plan'])
                ->latest()
                ->get();

            $data = $subscriptions->map(function($subscription, $index) use ($trashed) {
                return [
                    'DT_RowIndex' => $index + 1,
                    'user' => $subscription->user->name ?? 'N/A',
                    'plan' => $subscription->plan->name ?? 'N/A',
                    'status' => ucfirst($subscription->status),
                    'payway_status' => ucfirst($subscription->payway_status ?? 'N/A'),
                    'ends_at' => $subscription->ends_at
                        ? $subscription->ends_at->format('Y-m-d')
                        : 'N/A',
                    'payment_method' => $this->formatPaymentMethod($subscription),
                    'actions' => $this->generateActionButtons($subscription, $trashed)
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => $subscriptions->count(),
                'recordsFiltered' => $subscriptions->count(),
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('DataTable simple error:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Update your repository method if needed
     */
    public function getDataTableData(bool $trashed = false): Collection
    {
        $query = $trashed
            ? Subscription::onlyTrashed()
            : Subscription::whereNull('deleted_at');

        return $query->with(['user', 'plan'])
            ->latest()
            ->get();
    }

    /**
     * Generate action buttons for DataTable
     */
    private function generateActionButtons($subscription, $trashed = false)
    {
        $buttons = '';

        if ($trashed) {
            // Restore button for trashed items
            $buttons .= '<button type="button" class="btn btn-sm btn-success restore-btn"
                            data-id="' . $subscription->id . '" title="Restore">
                            <i class="fas fa-undo"></i>
                        </button> ';

            // Force delete button
            $buttons .= '<button type="button" class="btn btn-sm btn-danger force-delete-btn"
                            data-id="' . $subscription->id . '" title="Permanently Delete">
                            <i class="fas fa-trash"></i>
                        </button>';
        } else {
            // View button
            $buttons .= '<a href="' . route('admin.subscriptions.show', $subscription->id) . '"
                            class="btn btn-sm btn-info" title="View">
                            <i class="fas fa-eye"></i>
                        </a> ';

            // Edit button
            $buttons .= '<a href="' . route('admin.subscriptions.edit', $subscription->id) . '"
                            class="btn btn-sm btn-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a> ';

            // Cancel subscription button (if active)
            if ($subscription->status === 'active') {
                $buttons .= '<button type="button" class="btn btn-sm btn-warning cancel-btn"
                                data-id="' . $subscription->id . '" title="Cancel Subscription">
                                <i class="fas fa-ban"></i>
                            </button> ';
            }

            // Delete button
            $buttons .= '<button type="button" class="btn btn-sm btn-danger delete-btn"
                            data-id="' . $subscription->id . '" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>';
        }

        return $buttons;
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
    public function getUserSubscriptions(): JsonResponse
    {
        $user = Auth::user();
        try {
            $subscriptions = $this->subscriptionRepository->getByUser($user->id, ['plan']);

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
    
    public function getKids(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Get all kids belonging to the authenticated user
            $kids = Kid::where('user_id', $user->id)
                ->select('id', 'first_name', 'last_name')
                ->orderBy('id', 'DESC')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Kids retrieved successfully',
                'data' => $kids,
                'count' => $kids->count(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load kids',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
