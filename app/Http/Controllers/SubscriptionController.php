<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Http\Requests\CancelSubscriptionRequest;
use App\Services\PayWayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\app\Models\Plan;

class SubscriptionController extends Controller
{
    protected PayWayService $payWayService;

    public function __construct(PayWayService $payWayService)
    {
        $this->payWayService = $payWayService;
        $this->middleware('auth:sanctum');
    }

    /**
     * সব available plans list করার জন্য
     */
    public function getPlans(): JsonResponse
    {
        try {
            $plans = Plan::active()
                ->with('pickupType')
                ->orderBy('sort_order')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Plans retrieved successfully',
                'data' => $plans
            ]);

        } catch (\Exception $e) {
            Log::error('Plans retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Plans retrieval failed'
            ], 500);
        }
    }

    /**
     * User এর current subscription get করার জন্য
     */
    public function getCurrentSubscription(): JsonResponse
    {
        try {
            $user = Auth::user();
            $subscription = $user->subscriptions()
                ->active()
                ->with('plan.pickupType')
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => true,
                    'message' => 'No active subscription found',
                    'data' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Current subscription retrieved successfully',
                'data' => [
                    'subscription' => $subscription,
                    'on_trial' => $subscription->onTrial(),
                    'days_until_expires' => $subscription->daysUntilExpires(),
                    'can_cancel' => $subscription->active() && !$subscription->canceled(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Current subscription retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Subscription retrieval failed'
            ], 500);
        }
    }

    /**
     * নতুন subscription তৈরি করার জন্য
     */
    public function createSubscription(CreateSubscriptionRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // Check করি user এর কোন active subscription আছে কিনা
            $existingSubscription = $user->subscriptions()->active()->first();
            if ($existingSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active subscription'
                ], 422);
            }

            $plan = Plan::findOrFail($request->plan_id);

            DB::beginTransaction();

            // PayWay এ subscription তৈরি করি
            $result = $this->payWayService->createSubscription(
                $user,
                $plan,
                $request->token
            );

            if (!$result['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully',
                'data' => [
                    'subscription' => $result['subscription'],
                    'customer_number' => $result['customer_number'],
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Subscription creation failed'
            ], 500);
        }
    }

    /**
     * Subscription cancel করার জন্য
     */
    public function cancelSubscription(CancelSubscriptionRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $subscription = $user->subscriptions()
                ->active()
                ->findOrFail($request->subscription_id);

            $result = $this->payWayService->cancelSubscription($subscription);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 422);
            }

            // Cancellation reason save করি
            $subscription->update([
                'cancellation_reason' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription canceled successfully',
                'data' => $result['subscription']
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription cancellation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Subscription cancellation failed'
            ], 500);
        }
    }

    /**
     * Subscription resume করার জন্য
     */
    public function resumeSubscription(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $subscription = $user->subscriptions()
                ->where('id', $request->subscription_id)
                ->where('status', 'inactive')
                ->whereNotNull('canceled_at')
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found or cannot be resumed'
                ], 404);
            }

            // PayWay এ schedule আবার activate করি
            // (এটা PayWay API documentation অনুযায়ী implement করতে হবে)

            $subscription->update([
                'status' => 'active',
                'canceled_at' => null,
                'cancellation_reason' => null,
                'ends_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription resumed successfully',
                'data' => $subscription
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription resume failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Subscription resume failed'
            ], 500);
        }
    }

    /**
     * Payment history get করার জন্য
     */
    public function getPaymentHistory(): JsonResponse
    {
        try {
            $user = Auth::user();
            $subscriptions = $user->subscriptions()
                ->with('plan')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Payment history retrieved successfully',
                'data' => $subscriptions
            ]);

        } catch (\Exception $e) {
            Log::error('Payment history retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment history retrieval failed'
            ], 500);
        }
    }

    /**
     * PayWay publishable key get করার জন্য
     */
    public function getPublishableKey(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'publishable_key' => config('payway.publishable_key')
        ]);
    }
}

// app/Http/Requests/CreateSubscriptionRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id' => 'required|exists:plans,id',
            'token' => 'required|string', // PayWay single-use token
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required' => 'Plan selection is required',
            'plan_id.exists' => 'Selected plan is invalid',
            'token.required' => 'Payment token is required',
        ];
    }
}

// app/Http/Requests/CancelSubscriptionRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscription_id' => 'required|exists:subscriptions,id',
            'reason' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'subscription_id.required' => 'Subscription ID is required',
            'subscription_id.exists' => 'Subscription not found',
            'reason.max' => 'Cancellation reason cannot exceed 500 characters',
        ];
    }
}
