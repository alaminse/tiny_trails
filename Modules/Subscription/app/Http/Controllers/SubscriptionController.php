<?php

namespace Modules\Subscription\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Subscription\app\Models\Plan;
use Modules\Subscription\app\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Subscription::where('user_id', Auth::id())->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = Auth::user();

        if (!$user->hasRole('parent')) {
            return response()->json(['message' => 'Only parents can take subscriptions'], 403);
        }

        $plan = Plan::find($request->plan_id);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'name' => $plan->name,
            'stripe_id' => null,
            'stripe_status' => 'active',
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        return response()->json($subscription, 201);
    }
}
