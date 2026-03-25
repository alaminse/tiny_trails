<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\RideAssignment\app\Models\RideAssign;
use Modules\Subscription\app\Models\Subscription;
use Modules\UserRolePermission\app\Models\Driver;
use Modules\UserRolePermission\app\Models\Kid;

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

        if (! $parent->hasRole('parent')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        $subscriptionIds = Subscription::where('user_id', $parent->id)
            ->pluck('id')->toArray();

        // ── Today's rides ──────────────────────────────────────────────────────
        $todayRides = RideAssign::whereIn('subscription_id', $subscriptionIds)
            ->whereJsonContains('selected_dates', $today->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->whereNull('deleted_at')
            ->with([
                'subscription.user',
                'subscription.kid',
                'subscription.pickupLocation',
                'subscription.dropoffLocation',
                'rides.driver',  // শুধু driver পর্যন্ত
            ])
            ->get();

        // ── Weekly rides count ─────────────────────────────────────────────────
        $weekDates = collect();
        for ($i = 0; $i < 7; $i++) {
            $weekDates->push($thisWeek->copy()->addDays($i)->format('Y-m-d'));
        }
        $weeklyRides = RideAssign::whereIn('subscription_id', $subscriptionIds)
            ->where(function ($q) use ($weekDates) {
                foreach ($weekDates as $date) {
                    $q->orWhereJsonContains('selected_dates', $date);
                }
            })
            ->whereNull('deleted_at')
            ->count();

        // ── Monthly spending ───────────────────────────────────────────────────
        $monthDates = collect();
        for ($i = 0; $i < $thisMonth->daysInMonth; $i++) {
            $monthDates->push($thisMonth->copy()->addDays($i)->format('Y-m-d'));
        }
        $monthlySpending = RideAssign::whereIn('subscription_id', $subscriptionIds)
            ->where('status', 'completed')
            ->where(function ($q) use ($monthDates) {
                foreach ($monthDates as $date) {
                    $q->orWhereJsonContains('selected_dates', $date);
                }
            })
            ->whereNull('deleted_at')
            ->sum('fare');

        // ── Active subscription ────────────────────────────────────────────────
        $subscription = Subscription::where('user_id', $parent->id)
            ->where('status', 'active')
            ->first();

        // ── Next scheduled ride ────────────────────────────────────────────────
        $nextRide = null;
        $allUpcoming = RideAssign::whereIn('subscription_id', $subscriptionIds)
            ->where('status', '!=', 'cancelled')
            ->whereNull('deleted_at')
            ->with([
                'subscription.kid',
                'subscription.pickupLocation',
                'subscription.dropoffLocation',
            ])
            ->get();

        $nextDate = null;
        $nextRideAssign = null;

        foreach ($allUpcoming as $assign) {
            $dates = json_decode($assign->selected_dates, true) ?? [];
            foreach ($dates as $date) {
                $d = Carbon::parse($date);
                if ($d->isFuture() || $d->isToday()) {
                    if ($nextDate === null || $d->lt($nextDate)) {
                        $nextDate = $d;
                        $nextRideAssign = $assign;
                    }
                }
            }
        }

        if ($nextRideAssign) {
            $firstRide = $nextRideAssign->rides->first();
            $kid = $nextRideAssign->subscription?->kid;
            $driver = $firstRide?->driver;

            $nextRide = [
                'date' => $nextDate->format('Y-m-d'),
                'date_formatted' => $nextDate->format('D, d M Y'),
                'service_type' => $nextRideAssign->service_type,
                'fare' => $nextRideAssign->fare,
                'kid' => $kid ? [
                    'name' => $kid->first_name.' '.$kid->last_name,
                    'school' => $kid->school_name,
                ] : null,
                'pickup_location' => $nextRideAssign->subscription?->pickupLocation?->address,
                'dropoff_location' => $nextRideAssign->subscription?->dropoffLocation?->address,
                'driver' => $driver ? [
                    'name' => $driver->first_name.' '.$driver->last_name,
                    'phone' => $driver->phone,
                ] : null,
            ];
        }

        // ── Kids list ──────────────────────────────────────────────────────────
        $kids = Kid::where('user_id', $parent->id)
            ->whereNull('deleted_at')
            ->with(['pickupLocation', 'dropoffLocation'])
            ->get()
            ->map(fn ($kid) => [
                'id' => $kid->id,
                'name' => $kid->first_name.' '.$kid->last_name,
                'school' => $kid->school_name,
                'photo' => $kid->photo,
                'pickup_location' => $kid->pickupLocation?->address
                                      ?? $kid->pickup_location,
                'dropoff_location' => $kid->dropoffLocation?->address
                                      ?? $kid->dropoff_location,
            ]);

        // ── Recent rides ───────────────────────────────────────────────────────
        $recentDates = collect();
        for ($i = 0; $i < 7; $i++) {
            $recentDates->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }
        $recentRides = RideAssign::whereIn('subscription_id', $subscriptionIds)
            ->where(function ($q) use ($recentDates) {
                foreach ($recentDates as $date) {
                    $q->orWhereJsonContains('selected_dates', $date);
                }
            })
            ->whereNull('deleted_at')
            ->with([
                'subscription.kid',
                'subscription.pickupLocation',
                'subscription.dropoffLocation',
            ])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ── Response ───────────────────────────────────────────────────────────
        return response()->json([
            'success' => true,
            'data' => [

                'parent' => [
                    'name' => $parent->first_name.' '.$parent->last_name,
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
                    'days_remaining' => Carbon::now()->diffInDays(
                        $subscription->ends_at, false),
                    'price' => $subscription->price,
                    'card_brand' => $subscription->card_brand,
                    'card_last_four' => $subscription->card_last_four,
                ] : null,

                'kids' => $kids,
                'next_ride' => $nextRide,
                'today_rides' => $todayRides->map(function ($ride) {
                    $firstRide  = $ride->rides->first();
                    $driverUser = $firstRide?->driver; // User model
                    $kid = $ride->subscription?->kid;

                    return [
                        'id'               => $ride->id,
                        'service_type'     => $ride->service_type,
                        'status'           => $ride->status,
                        'fare'             => $ride->fare,
                        'total_days'       => $ride->total_days,
                        'pickup_location'  => $ride->subscription?->pickupLocation?->address,
                        'dropoff_location' => $ride->subscription?->dropoffLocation?->address,
                        'kid' => $kid ? [
                            'name'   => $kid->first_name . ' ' . $kid->last_name,
                            'school' => $kid->school_name,
                        ] : null,
                        'driver' => $driverUser ? [
                            'name'         => $driverUser->first_name . ' ' . $driverUser->last_name,
                            'phone'        => $driverUser->phone,
                        ] : null,
                    ];
                }),

                'recent_rides' => $recentRides->map(function ($ride) {
                    $firstRide = $ride->rides->first();
                    $driver = $firstRide?->driver;
                    $kid = $ride->subscription?->kid;

                    return [
                        'id' => $ride->id,
                        'service_type' => $ride->service_type,
                        'status' => $ride->status,
                        'fare' => $ride->fare,
                        'total_days' => $ride->total_days,
                        'selected_dates' => json_decode($ride->selected_dates),
                        'pickup_location' => $ride->subscription
                            ?->pickupLocation?->address,
                        'dropoff_location' => $ride->subscription
                            ?->dropoffLocation?->address,
                        'kid' => $kid
                            ? $kid->first_name.' '.$kid->last_name
                            : null,
                        'driver' => $driver
                            ? $driver->first_name.' '.$driver->last_name
                            : null,
                        'created_at' => $ride->created_at,
                    ];
                }),
            ],
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
                            'name' => $ride->driver->first_name.' '.$ride->driver->last_name,
                            'phone' => $ride->driver->phone,
                        ] : null,
                        'kid' => $ride->kid ? [
                            'name' => $ride->kid->first_name.' '.$ride->kid->last_name,
                            'age' => $ride->kid->dob ? Carbon::parse($ride->kid->dob)->age : null,
                        ] : null,
                    ];
                }),
            ],
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
                'rides' => $rides,
                // 'rides' => RideResource::collection($rides),
            ],
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

        if (! $ride) {
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
            ],
        ]);
    }
}
