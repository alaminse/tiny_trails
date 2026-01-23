<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RideResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Modules\RideAssignment\app\Models\RideAssign;
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

    // Parent এর সব subscription IDs বের করা
    $subscriptionIds = Subscription::where('user_id', $parent->id)
        ->pluck('id')
        ->toArray();

    // আজকের rides
    $todayRides = RideAssign::whereIn('subscription_id', $subscriptionIds)
        ->whereJsonContains('selected_dates', $today->format('Y-m-d'))
        ->where('status', '!=', 'cancelled')
        ->whereNull('deleted_at')
        ->with(['subscription.user', 'rides.driver']) // এখানে rides.driver
        ->get();

    // সপ্তাহের rides count
    $weekDates = collect();
    for ($i = 0; $i < 7; $i++) {
        $weekDates->push($thisWeek->copy()->addDays($i)->format('Y-m-d'));
    }

    $weeklyRides = RideAssign::whereIn('subscription_id', $subscriptionIds)
        ->where(function($query) use ($weekDates) {
            foreach ($weekDates as $date) {
                $query->orWhereJsonContains('selected_dates', $date);
            }
        })
        ->whereNull('deleted_at')
        ->count();

    // মাসের খরচ
    $monthDates = collect();
    $daysInMonth = $thisMonth->copy()->daysInMonth;
    for ($i = 0; $i < $daysInMonth; $i++) {
        $monthDates->push($thisMonth->copy()->addDays($i)->format('Y-m-d'));
    }

    $monthlySpending = RideAssign::whereIn('subscription_id', $subscriptionIds)
        ->where('status', 'completed')
        ->where(function($query) use ($monthDates) {
            foreach ($monthDates as $date) {
                $query->orWhereJsonContains('selected_dates', $date);
            }
        })
        ->whereNull('deleted_at')
        ->sum('fare');

    // Active subscription
    $subscription = Subscription::where('user_id', $parent->id)
        ->where('status', 'active')
        ->first();

    // সাম্প্রতিক rides - শেষ ৭ দিন
    $recentDates = collect();
    for ($i = 0; $i < 7; $i++) {
        $recentDates->push(Carbon::now()->subDays($i)->format('Y-m-d'));
    }

    // সাম্প্রতিক rides
    $recentRides = RideAssign::whereIn('subscription_id', $subscriptionIds)
        ->where(function($query) use ($recentDates) {
            foreach ($recentDates as $date) {
                $query->orWhereJsonContains('selected_dates', $date);
            }
        })
        ->whereNull('deleted_at')
        ->with(['subscription.user', 'rides.driver']) // এখানে rides.driver
        ->orderBy('created_at', 'desc')
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
                $firstRide = $ride->rides->first(); // প্রথম ride নিন
    
                return [
                    'id' => $ride->id,
                    'service_type' => $ride->service_type,
                    'status' => $ride->status,
                    'fare' => $ride->fare,
                    'driver_commission' => $ride->driver_commission,
                    'platform_commission' => $ride->platform_commission,
                    'total_days' => $ride->total_days,
                    'driver' => $firstRide && $firstRide->driver ? [
                        'name' => $firstRide->driver->first_name . ' ' . $firstRide->driver->last_name,
                        'phone' => $firstRide->driver->phone,
                    ] : null,
                    'subscription' => $ride->subscription ? [
                        'parent_name' => $ride->subscription->user->first_name . ' ' . $ride->subscription->user->last_name,
                    ] : null,
                ];
            }),
            'recent_rides' => $recentRides->map(function ($ride) {
                $firstRide = $ride->rides->first();
    
                return [
                    'id' => $ride->id,
                    'service_type' => $ride->service_type,
                    'status' => $ride->status,
                    'fare' => $ride->fare,
                    'total_days' => $ride->total_days,
                    'selected_dates' => json_decode($ride->selected_dates),
                    'driver' => $firstRide && $firstRide->driver 
                        ? $firstRide->driver->first_name . ' ' . $firstRide->driver->last_name 
                        : null,
                    'created_at' => $ride->created_at,
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
        
        $rides = RideAssign::where('parent_id', $parent->id)
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

        $query = RideAssign::where('parent_id', $parent->id)
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
        $ride = RideAssign::where('id', $rideId)
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