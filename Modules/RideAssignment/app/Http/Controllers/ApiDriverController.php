<?php

namespace Modules\RideAssignment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\RideAssignment\app\Models\Ride;

class ApiDriverController extends Controller
{
    // Driver Schedule API - Get all upcoming schedule grouped by date
    public function schedule()
    {
        $driver = Auth::user(); // Assuming authenticated driver

        // Get rides from today onwards, grouped by date
        $rides = Ride::where('driver_id', $driver->id)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->orderBy('pickup')
            ->get()
            ->groupBy('date')
            ->map(function ($dayRides, $date) {
                return [
                    'date' => $date,
                    'day_name' => Carbon::parse($date)->format('l'), // Monday, Tuesday, etc.
                    'formatted_date' => Carbon::parse($date)->format('M d, Y'), // Sep 28, 2025
                    'is_today' => Carbon::parse($date)->isToday(),
                    'is_tomorrow' => Carbon::parse($date)->isTomorrow(),
                    'rides' => $dayRides->map(function ($ride) {
                        return [
                            'id' => $ride->id,
                            'ride_type' => $ride->ride_type,
                            'pickup_time' => Carbon::parse($ride->pickup)->format('h:i A'),
                            'drop_off_time' => Carbon::parse($ride->drop_off)->format('h:i A'),
                            'parent_name' => $ride->parent_name ?? 'N/A',
                            'parent_phone' => $ride->parent_phone ?? null,
                            'pickup_location' => $ride->pickupLocation->address ?? null,
                            'dropoff_location' => $ride->dropoffLocation->address ?? null,
                            'commission' => $ride->commission,
                            'status' => $ride->status
                        ];
                    }),
                    'total_rides' => $dayRides->count(),
                    'total_commission' => $dayRides->sum('commission')
                ];
            })
            ->values(); // Reset array keys

        return response()->json([
            'success' => true,
            'data' => $rides
        ]);
    }

    // Get specific date schedule for driver
    public function getDriverDateSchedule(Request $request)
    {
        $driver = Auth::user();
        $date = $request->get('date', now()->format('Y-m-d'));

        $rides = Ride::where('driver_id', $driver->id)
            ->whereDate('date', $date)
            ->where('date', '>=', now()->format('Y-m-d')) // Only future dates
            ->orderBy('pickup')
            ->get()
            ->map(function ($ride) {
                return [
                    'id' => $ride->id,
                    'ride_type' => $ride->ride_type,
                    'pickup_time' => Carbon::parse($ride->pickup)->format('h:i A'),
                    'drop_off_time' => Carbon::parse($ride->drop_off)->format('h:i A'),
                    'parent_name' => $ride->parent_name ?? 'N/A',
                    'parent_phone' => $ride->parent_phone ?? null,
                    'pickup_location' => $ride->pickupLocation?->address ?? null,
                    'dropoff_location' => $ride->dropoffLocation?->address ?? null,
                    'commission' => $ride->commission,
                    'status' => $ride->status
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'day_name' => Carbon::parse($date)->format('l'),
                'formatted_date' => Carbon::parse($date)->format('M d, Y'),
                'is_today' => Carbon::parse($date)->isToday(),
                'rides' => $rides,
                'total_rides' => $rides->count(),
                'total_commission' => $rides->sum('commission')
            ]
        ]);
    }

    // Update ride status by driver
    public function updateRideStatus(Request $request, $rideId)
    {
        $request->validate([
            'status' => 'required|in:in_progress,arrive_home,start_ride,completed,cancelled'
        ]);

        $driver = Auth::user();

        $ride = Ride::where('id', $rideId)
            ->where('driver_id', $driver->id)
            ->first();

        if (!$ride) {
            return response()->json([
                'success' => false,
                'message' => 'Ride not found or not assigned to you'
            ], 404);
        }

        $ride->update(['status' => $request->status]);

        // Send notification to parent (you can implement this later)
        // $this->sendNotificationToParent($ride, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Ride status updated successfully',
            'data' => [
                'ride_id' => $ride->id,
                'new_status' => $request->status,
                'updated_at' => $ride->updated_at
            ]
        ]);
    }

    // Get driver dashboard stats
    public function driverDashboard()
    {
        $driver = Auth::user();

        // Today's rides
        $todayRides = Ride::where('driver_id', $driver->id)
            ->whereDate('date', today())
            ->get();

        // This week's rides
        $thisWeekRides = Ride::where('driver_id', $driver->id)
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->get();

        // This month's rides
        $thisMonthRides = Ride::where('driver_id', $driver->id)
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->get();

        $data = [
            'today' => [
                'total_rides' => $todayRides->count(),
                'completed_rides' => $todayRides->where('status', 'completed')->count(),
                'pending_rides' => $todayRides->whereIn('status', ['assigned', 'in_progress'])->count(),
                'total_commission' => $todayRides->sum('commission'),
                'rides' => $todayRides->map(function($ride) {
                    return [
                        'id' => $ride->id,
                        'ride_type' => $ride->ride_type,
                        'pickup_time' => Carbon::parse($ride->pickup)->format('h:i A'),
                        'status' => $ride->status,
                        'commission' => $ride->commission
                    ];
                })
            ],
            'this_week' => [
                'total_rides' => $thisWeekRides->count(),
                'completed_rides' => $thisWeekRides->where('status', 'completed')->count(),
                'total_commission' => $thisWeekRides->sum('commission')
            ],
            'this_month' => [
                'total_rides' => $thisMonthRides->count(),
                'completed_rides' => $thisMonthRides->where('status', 'completed')->count(),
                'total_commission' => $thisMonthRides->sum('commission')
            ],
            'upcoming_rides' => Ride::where('driver_id', $driver->id)
                ->where('date', '>', now())
                ->orderBy('date')
                ->orderBy('pickup')
                ->take(5)
                ->get()
                ->map(function($ride) {
                    return [
                        'id' => $ride->id,
                        'date' => $ride->date,
                        'ride_type' => $ride->ride_type,
                        'pickup_time' => Carbon::parse($ride->pickup)->format('h:i A'),
                        'commission' => $ride->commission
                    ];
                })
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Get driver earnings
    // public function driverEarnings(Request $request)
    // {
    //     $driver = Auth::user();
    //     $period = $request->get('period', 'this_month'); // this_week, this_month, all_time

    //     $query = Ride::where('driver_id', $driver->id);

    //     switch ($period) {
    //         case 'this_week':
    //             $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    //             break;
    //         case 'this_month':
    //             $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
    //             break;
    //         case 'all_time':
    //             // No date filter
    //             break;
    //     }

    //     $rides = $query->get();

    //     $data = [
    //         'period' => $period,
    //         'total_rides' => $rides->count(),
    //         'completed_rides' => $rides->where('status', 'completed')->count(),
    //         'total_earnings' => $rides->sum('commission'),
    //         'completed_earnings' => $rides->where('status', 'completed')->sum('commission'),
    //         'pending_earnings' => $rides->whereIn('status', ['assigned', 'in_progress'])->sum('commission'),
    //         'earnings_breakdown' => $rides->groupBy('status')->map(function($statusRides, $status) {
    //             return [
    //                 'status' => $status,
    //                 'count' => $statusRides->count(),
    //                 'earnings' => $statusRides->sum('commission')
    //             ];
    //         })->values()
    //     ];

    //     return response()->json([
    //         'success' => true,
    //         'data' => $data
    //     ]);
    // }
}
