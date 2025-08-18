<?php

namespace Modules\Subscription\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Subscription\app\Models\Plan;
use Modules\Subscription\app\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Modules\Subscription\app\Http\Requests\SubscriptionRequest;
use Modules\Subscription\app\Http\Resources\PlanResource;
use Modules\Subscription\app\Http\Resources\SubscriptionResource;
use Modules\Subscription\app\Repositories\SubscriptionRepository;

class SubscriptionController extends Controller
{
    protected $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $subscriptions = $this->subscriptionRepository->getData($request);
        return response()->json(SubscriptionResource::collection($subscriptions));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function buynow(SubscriptionRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // The authorization check is now handled in the SubscriptionRequest.
        $plan = Plan::find($request->plan_id);

        $existingSubscription = $this->subscriptionRepository->findActiveByUserAndPlan($user->id, $plan->id);

        if ($existingSubscription) {
            return response()->json([
                'message' => 'You already have an active subscription until ' 
                    . $existingSubscription->trial_ends_at->format('Y-m-d H:i:s'),
            ], 422);
        }

        $subscription = $this->subscriptionRepository->create([
            'user_id'       => $user->id,
            'plan_id'       => $request->plan_id,
            'name'          => $plan->name,
            'stripe_id'     => null, // This should be populated by your payment gateway
            'stripe_status' => 'active', // This is a placeholder
            'trial_ends_at' => now()->addDays($plan->trial_days),
            'ends_at'       => null,
        ]);

        return response()->json(new SubscriptionResource($subscription), 201);
    }

    /**
     * Display the specified resource.
     */
    public function details(Subscription $subscription)
    {
        $subscription->load(['user', 'plan']);
        return response()->json(new SubscriptionResource($subscription));
    }
    public function planDetails(Plan $plan)
    {
        return response()->json(new PlanResource($plan));
    }

    /**
     * Soft delete a subscription.
     */
    public function destroy(Subscription $subscription)
    {
        $this->subscriptionRepository->delete($subscription->id);
        return response()->json(['message' => 'Subscription deleted successfully.']);
    }

    /**
     * Restore a soft-deleted subscription.
     */
    public function restore(int $id)
    {
        $this->subscriptionRepository->restore($id);
        return response()->json(['message' => 'Subscription restored successfully.']);
    }


    public function plans()
    {
        $plan = Plan::where('status', 'active')->get();
        return response()->json(PlanResource::collection($plan));
    }
}
