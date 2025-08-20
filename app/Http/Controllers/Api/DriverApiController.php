<?php

// App/Http/Controllers/Api/DriverApiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Modules\DriverCommission\app\Models\DriverCommission;
use Modules\DriverCommission\app\Models\DriverEarningsSummary;
use Modules\RideAssignment\app\Models\RideAssignment;

class DriverApiController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }

    /**
     * Driver Dashboard Data
     */
    public function dashboard(Request $request): JsonResponse
    {
        $driver = $request->user();
        
        if (!$driver->hasRole('driver')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // Today's rides
        $todayRides = RideAssignment::where('driver_id', $driver->id)
            ->whereDate('ride_date', $today)
            ->orderBy('pickup_time')
            ->get();

        // Today's earnings
        $todayEarnings = DriverCommission::where('driver_id', $driver->id)
            ->whereDate('earning_date', $today)
            ->sum('total_earning');

        // Weekly earnings
        $weeklyEarnings = DriverCommission::where('driver_id', $driver->id)
            ->where('earning_date', '>=', $thisWeek)
            ->sum('total_earning');

        // Monthly earnings
        $monthlyEarnings = DriverCommission::where('driver_id', $driver->id)
            ->where('earning_date', '>=', $thisMonth)
            ->sum('total_earning');

        // Pending payments
        $pendingPayments = DriverCommission::where('driver_id', $driver->id)
            ->where('payment_status', 'pending')
            ->sum('total_earning');

        // Recent rides status
        $recentRides = RideAssignment::where('driver_id', $driver->id)
            ->where('ride_date', '>=', Carbon::now()->subDays(7))
            ->orderBy('ride_date', 'desc')
            ->orderBy('pickup_time', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'driver' => [
                    'name' => $driver->first_name . ' ' . $driver->last_name,
                    'email' => $driver->email,
                    'phone' => $driver->phone,
                    'status' => $driver->status,
                ],
                'earnings' => [
                    'today' => number_format($todayEarnings, 2),
                    'weekly' => number_format($weeklyEarnings, 2),
                    'monthly' => number_format($monthlyEarnings, 2),
                    'pending_payments' => number_format($pendingPayments, 2),
                ],
                'today_rides' => $todayRides->map(function ($ride) {
                    return [
                        'id' => $ride->id,
                        'title' => $ride->ride_title,
                        'pickup_location' => $ride->pickup_location,
                        'dropoff_location' => $ride->dropoff_location,
                        'pickup_time' => $ride->pickup_time,
                        'fare' => $ride->ride_fare,
                        'status' => $ride->status,
                        'parent_name' => $ride->parent ? $ride->parent->first_name . ' ' . $ride->parent->last_name : null,
                        'kid_name' => $ride->kid ? $ride->kid->first_name . ' ' . $ride->kid->last_name : null,
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
                        'commission' => $ride->driver_commission,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Driver's Scheduled Rides
     */
    public function schedule(Request $request): JsonResponse
    {
        $driver = $request->user();
        $date = $request->get('date', Carbon::today()->toDateString());
        
        $rides = RideAssignment::where('driver_id', $driver->id)
            ->whereDate('ride_date', $date)
            ->with(['parent', 'kid'])
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
                        'pickup_latitude' => $ride->pickup_latitude,
                        'pickup_longitude' => $ride->pickup_longitude,
                        'dropoff_latitude' => $ride->dropoff_latitude,
                        'dropoff_longitude' => $ride->dropoff_longitude,
                        'pickup_time' => $ride->pickup_time,
                        'estimated_dropoff_time' => $ride->estimated_dropoff_time,
                        'distance_km' => $ride->distance_km,
                        'estimated_duration_minutes' => $ride->estimated_duration_minutes,
                        'fare' => $ride->ride_fare,
                        'commission' => $ride->driver_commission,
                        'status' => $ride->status,
                        'special_instructions' => $ride->special_instructions,
                        'parent' => $ride->parent ? [
                            'name' => $ride->parent->first_name . ' ' . $ride->parent->last_name,
                            'phone' => $ride->parent->phone,
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
     * Driver's Earnings History
     */
    public function earnings(Request $request): JsonResponse
    {
        $driver = $request->user();
        $type = $request->get('type', 'commission'); // commission, summary
        $period = $request->get('period', 'monthly'); // daily, weekly, monthly
        $limit = $request->get('limit', 20);

        if ($type === 'commission') {
            $earnings = DriverCommission::where('driver_id', $driver->id)
                ->orderBy('earning_date', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'type' => 'commission',
                    'earnings' => $earnings->map(function ($commission) {
                        return [
                            'id' => $commission->id,
                            'date' => $commission->earning_date,
                            'type' => $commission->commission_type,
                            'base_fare' => $commission->base_fare,
                            'commission_rate' => $commission->commission_rate,
                            'commission_amount' => $commission->commission_amount,
                            'bonus_amount' => $commission->bonus_amount,
                            'penalty_amount' => $commission->penalty_amount,
                            'total_earning' => $commission->total_earning,
                            'payment_status' => $commission->payment_status,
                            'payment_date' => $commission->payment_date,
                            'payment_method' => $commission->payment_method,
                            'description' => $commission->description,
                        ];
                    })
                ]
            ]);
        } else {
            $summaries = DriverEarningsSummary::where('driver_id', $driver->id)
                ->where('summary_type', $period)
                ->orderBy('summary_date', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'type' => 'summary',
                    'period' => $period,
                    'summaries' => $summaries->map(function ($summary) {
                        return [
                            'date' => $summary->summary_date,
                            'total_rides' => $summary->total_rides,
                            'completed_rides' => $summary->completed_rides,
                            'cancelled_rides' => $summary->cancelled_rides,
                            'total_fare' => $summary->total_fare,
                            'total_commission' => $summary->total_commission,
                            'total_bonus' => $summary->total_bonus,
                            'total_penalty' => $summary->total_penalty,
                            'net_earnings' => $summary->net_earnings,
                            'completion_rate' => $summary->completion_rate,
                            'average_rating' => $summary->average_rating,
                            'total_distance_km' => $summary->total_distance_km,
                            'total_duration_minutes' => $summary->total_duration_minutes,
                        ];
                    })
                ]
            ]);
        }
    }

    /**
     * Update Ride Status
     */
    public function updateRideStatus(Request $request, $rideId): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:accepted,started,completed,cancelled',
            'reason' => 'nullable|string|max:500',
        ]);

        $driver = $request->user();
        $ride = RideAssignment::where('id', $rideId)
            ->where('driver_id', $driver->id)
            ->first();

        if (!$ride) {
            return response()->json(['error' => 'Ride not found'], 404);
        }

        $now = Carbon::now();
        $updateData = ['status' => $request->status];

        switch ($request->status) {
            case 'accepted':
                $updateData['accepted_at'] = $now;
                break;
            case 'started':
                $updateData['started_at'] = $now;
                break;
            case 'completed':
                $updateData['completed_at'] = $now;
                break;
            case 'cancelled':
                $updateData['cancelled_at'] = $now;
                $updateData['cancelled_by'] = 'driver';
                $updateData['cancellation_reason'] = $request->reason;
                break;
        }

        $ride->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Ride status updated successfully',
            'data' => [
                'ride_id' => $ride->id,
                'status' => $ride->status,
                'updated_at' => $ride->updated_at,
            ]
        ]);
    }
}