<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RideResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Modules\RideAssignment\app\Models\RideAssignment;
use Modules\Subscription\app\Models\Subscription;

class ParentApiController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }

    /**
     * Parent Dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        $parent = $request->user();
        
        if (!$parent->hasRole('parent')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // Today's rides
        $todayRides = RideAssignment::where('parent_id', $parent->id)
            ->whereDate('ride_date', $today)
            ->with(['driver', 'kid'])
            ->orderBy('pickup_time')
            ->get();

        // Weekly rides
        $weeklyRides = RideAssignment::where('parent_id', $parent->id)
            ->where('ride_date', '>=', $thisWeek)
            ->count();

        // Monthly spending
        $monthlySpending = RideAssignment::where('parent_id', $parent->id)
            ->where('ride_date', '>=', $thisMonth)
            ->where('status', 'completed')
            ->sum('ride_fare');

        // Active subscription
        $subscription = Subscription::where('user_id', $parent->id)
            ->where('status', 'active')
            ->first();

        // Recent rides
        $recentRides = RideAssignment::where('parent_id', $parent->id)
            ->where('ride_date', '>=', Carbon::now()->subDays(7))
            ->with(['driver', 'kid'])
            ->orderBy('ride_date', 'desc')
            ->orderBy('pickup_time', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'parent' => [
                    'name' => $parent->first_name . ' ' . $parent->last_name,
                    'email' => $parent->email,
                    'phone' => $parent->phone,
                ],
                'stats' => [
                    'today_rides' => $todayRides->count(),
                    'weekly_rides' => $weeklyRides,
                    'monthly_spending' => number_format($monthlySpending, 2),
                ],
                'subscription' => $subscription ? [
                    'plan_name' => $subscription->name,
                    'status' => $subscription->status,
                    'ends_at' => $subscription->ends_at,
                    'price' => $subscription->price,
                ] : null,
                'today_rides' => $todayRides->map(function ($ride) {
                    return [
                        'id' => $ride->id,
                        'title' => $ride->ride_title,
                        'pickup_location' => $ride->pickup_location,
                        'dropoff_location' => $ride->dropoff_location,
                        'pickup_time' => $ride->pickup_time,
                        'status' => $ride->status,
                        'fare' => $ride->ride_fare,
                        'driver' => $ride->driver ? [
                            'name' => $ride->driver->first_name . ' ' . $ride->driver->last_name,
                            'phone' => $ride->driver->phone,
                        ] : null,
                        'kid' => $ride->kid ? [
                            'name' => $ride->kid->first_name . ' ' . $ride->kid->last_name,
                        ] : null,
                    ];
                }),
                'recent_rides' => $recentRides->map(function ($ride) {
                    return [
                        'id' => $ride->id,
                        'title' => $ride->ride_title,
                        'date' => $ride->ride_date,
                        'pickup_time' => $ride->pickup_time,
                        'status' => $ride->status,
                        'fare' => $ride->ride_fare,
                        'driver' => $ride->driver ? $ride->driver->first_name . ' ' . $ride->driver->last_name : null,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Parent's Scheduled Rides
     */
    public function schedule(Request $request): JsonResponse
    {
        $parent = $request->user();
        $date = $request->get('date', Carbon::today()->toDateString());
        
        $rides = RideAssignment::where('parent_id', $parent->id)
            ->whereDate('ride_date', $date)
            ->with(['driver', 'kid'])
            ->orderBy('pickup_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'total_rides' => $rides->count(),
                'rides' => $rides->map(function ($ride) {
                    return [
                        'id' => $ride->id,
                        'title' => $ride->ride_title,
                        'pickup_location' => $ride->pickup_location,
                        'dropoff_location' => $ride->dropoff_location,
                        'pickup_time' => $ride->pickup_time,
                        'estimated_dropoff_time' => $ride->estimated_dropoff_time,
                        'distance_km' => $ride->distance_km,
                        'estimated_duration_minutes' => $ride->estimated_duration_minutes,
                        'fare' => $ride->ride_fare,
                        'status' => $ride->status,
                        'special_instructions' => $ride->special_instructions,
                        'notes' => $ride->notes,
                        'driver' => $ride->driver ? [
                            'name' => $ride->driver->first_name . ' ' . $ride->driver->last_name,
                            'phone' => $ride->driver->phone,
                        ] : null,
                        'kid' => $ride->kid ? [
                            'name' => $ride->kid->first_name . ' ' . $ride->kid->last_name,
                            'age' => $ride->kid->dob ? Carbon::parse($ride->kid->dob)->age : null,
                        ] : null,
                    ];
                })
            ]
        ]);
    }

    /**
     * Parent's Ride History
     */
    public function history(Request $request): JsonResponse
    {
        $parent = $request->user();
        $status = $request->get('status', 'all'); // all, completed, cancelled
        $limit = $request->get('limit', 20);
        $page = $request->get('page', 1);

        $query = RideAssignment::where('parent_id', $parent->id)
            ->with(['driver', 'kid']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $rides = $query->orderBy('ride_date', 'desc')
            ->orderBy('pickup_time', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => [
                'status_filter' => $status,
                'total' => $rides->total(),
                'current_page' => $rides->currentPage(),
                'last_page' => $rides->lastPage(),
                'rides' => RideResource::collection($rides)
            ]
        ]);
        // return response()->json([
        //     'success' => true,
        //     'data' => [
        //         'status_filter' => $status,
        //         'total' => $rides->total(),
        //         'current_page' => $rides->currentPage(),
        //         'last_page' => $rides->lastPage(),
        //         'rides' => $rides->items()->map(function ($ride) {
        //             return [
        //                 'id' => $ride->id,
        //                 'title' => $ride->ride_title,
        //                 'date' => $ride->ride_date,
        //                 'pickup_time' => $ride->pickup_time,
        //                 'pickup_location' => $ride->pickup_location,
        //                 'dropoff_location' => $ride->dropoff_location,
        //                 'fare' => $ride->ride_fare,
        //                 'status' => $ride->status,
        //                 'completed_at' => $ride->completed_at,
        //                 'cancelled_at' => $ride->cancelled_at,
        //                 'cancellation_reason' => $ride->cancellation_reason,
        //                 'driver' => $ride->driver ? [
        //                     'name' => $ride->driver->first_name . ' ' . $ride->driver->last_name,
        //                     'phone' => $ride->driver->phone,
        //                 ] : null,
        //                 'kid' => $ride->kid ? [
        //                     'name' => $ride->kid->first_name . ' ' . $ride->kid->last_name,
        //                 ] : null,
        //             ];
        //         })
        //     ]
        // ]);
    }

    /**
     * Cancel Ride
     */
    public function cancelRide(Request $request, $rideId): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $parent = $request->user();
        $ride = RideAssignment::where('id', $rideId)
            ->where('parent_id', $parent->id)
            ->first();

        if (!$ride) {
            return response()->json(['error' => 'Ride not found'], 404);
        }

        if (in_array($ride->status, ['completed', 'cancelled'])) {
            return response()->json(['error' => 'Cannot cancel this ride'], 400);
        }

        $ride->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
            'cancelled_by' => 'parent',
            'cancellation_reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ride cancelled successfully',
            'data' => [
                'ride_id' => $ride->id,
                'status' => $ride->status,
                'cancelled_at' => $ride->cancelled_at,
            ]
        ]);
    }
}