<?php

namespace Modules\RideAssignment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\RideAssignment\app\Models\Ride;
use App\Traits\Upload;
use Illuminate\Support\Facades\Validator;

class ApiDriverController extends Controller
{

    use Upload;
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
            'status' => 'required|in:going_to_pickup,arrived_at_pickup,in_progress,completed,cancelled'
        ]);

        $ride = Ride::find($rideId);

        if (!$ride) {
            return response()->json([
                'success' => false,
                'message' => 'Ride not found or not assigned to you'
            ], 404);
        }

        $oldStatus = $ride->status;
        $ride->update(['status' => $request->status]);

        // Send notification to parent
        $this->sendRideStatusNotification($ride, $request->status, $oldStatus);

        return response()->json([
            'success' => true,
            'message' => 'Ride status updated successfully',
            'data' => [
                'ride_id' => $ride->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'updated_at' => $ride->updated_at
            ]
        ]);
    }

    private function sendRideStatusNotification($ride, $newStatus, $oldStatus)
    {
        // Get parent FCM token (assuming you have parent_id in rides table)
        $parent = User::find($ride->parent_id);

        if (!$parent || !$parent->fcm_token) {
            return;
        }

        $driver = $ride->driver;

        // Create notification messages based on status
        $notifications = [
            'going_to_pickup' => [
                'title' => 'Driver is on the way',
                'body' => "{$driver->name} is heading to pickup location"
            ],
            'arrive_home' => [
                'title' => 'Driver has arrived',
                'body' => "{$driver->name} has arrived at pickup location"
            ],
            'start_ride' => [
                'title' => 'Ride started',
                'body' => "{$driver->name} has started the {$ride->ride_type} ride"
            ],
            'completed' => [
                'title' => 'Ride completed',
                'body' => "Your {$ride->ride_type} ride has been completed successfully"
            ],
            'cancelled' => [
                'title' => 'Ride cancelled',
                'body' => "Your {$ride->ride_type} ride has been cancelled"
            ]
        ];

        $notification = $notifications[$newStatus] ?? [
            'title' => 'Ride Status Updated',
            'body' => "Your ride status has been updated to {$newStatus}"
        ];

        $data = [
            'ride_id' => (string)$ride->id,
            'status' => $newStatus,
            'ride_type' => $ride->ride_type,
            'driver_name' => $driver->name,
            'updated_at' => $ride->updated_at->toDateTimeString()
        ];

        // Send notification using Firebase service
        $firebaseService = app(FirebaseNotificationService::class);
        $firebaseService->sendNotification(
            $parent->fcm_token,
            $notification['title'],
            $notification['body'],
            $data
        );

        // Optional: Save notification to database for history
        Notification::create([
            'user_id'   => $parent->id,
            'title'     => $notification['title'],
            'message'   => $notification['body'],
            'data'      => json_encode($data),
            'type'      => 'ride_status_update',
            'read_at'   => null
        ]);
    }

    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = Auth::user();
        $user->update(['fcm_token' => $request->fcm_token]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token updated successfully'
        ]);
    }

    public function getNotifications(Request $request)
    {
        $user = Auth::user();

        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function markNotificationAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();

        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $user->id)
            ->first();

        if ($notification) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    public function uploadPhoto(Request $request, $rideId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'photo'     => 'nullable|image',
                'end_pic'   => 'nullable|image',
                ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $ride = Ride::findOrFail($rideId);
            $data = [];

            if($request->file('photo'))
            {
                $data['selfie'] = $this->uploadFile($request->file('photo'), 'ride/selfie');
            }

            if($request->file('end_pic'))
            {
                $data['end_pic'] = $this->uploadFile($request->file('end_pic'), 'ride/end_pic');
            }

            $ride->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully',
                'data' => [
                    'ride' => $ride,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Get driver dashboard stats
    // public function driverDashboard()
    // {
    //     $driver = Auth::user();

    //     // Today's rides
    //     $todayRides = Ride::where('driver_id', $driver->id)
    //         ->whereDate('date', today())
    //         ->get();

    //     // This week's rides
    //     $thisWeekRides = Ride::where('driver_id', $driver->id)
    //         ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
    //         ->get();

    //     // This month's rides
    //     $thisMonthRides = Ride::where('driver_id', $driver->id)
    //         ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
    //         ->get();

    //     $data = [
    //         'today' => [
    //             'total_rides' => $todayRides->count(),
    //             'completed_rides' => $todayRides->where('status', 'completed')->count(),
    //             'pending_rides' => $todayRides->whereIn('status', ['assigned', 'in_progress'])->count(),
    //             'total_commission' => $todayRides->sum('commission'),
    //             'rides' => $todayRides->map(function($ride) {
    //                 return [
    //                     'id' => $ride->id,
    //                     'ride_type' => $ride->ride_type,
    //                     'pickup_time' => Carbon::parse($ride->pickup)->format('h:i A'),
    //                     'status' => $ride->status,
    //                     'commission' => $ride->commission
    //                 ];
    //             })
    //         ],
    //         'this_week' => [
    //             'total_rides' => $thisWeekRides->count(),
    //             'completed_rides' => $thisWeekRides->where('status', 'completed')->count(),
    //             'total_commission' => $thisWeekRides->sum('commission')
    //         ],
    //         'this_month' => [
    //             'total_rides' => $thisMonthRides->count(),
    //             'completed_rides' => $thisMonthRides->where('status', 'completed')->count(),
    //             'total_commission' => $thisMonthRides->sum('commission')
    //         ],
    //         'upcoming_rides' => Ride::where('driver_id', $driver->id)
    //             ->where('date', '>', now())
    //             ->orderBy('date')
    //             ->orderBy('pickup')
    //             ->take(5)
    //             ->get()
    //             ->map(function($ride) {
    //                 return [
    //                     'id' => $ride->id,
    //                     'date' => $ride->date,
    //                     'ride_type' => $ride->ride_type,
    //                     'pickup_time' => Carbon::parse($ride->pickup)->format('h:i A'),
    //                     'commission' => $ride->commission
    //                 ];
    //             })
    //     ];

    //     return response()->json([
    //         'success' => true,
    //         'data' => $data
    //     ]);
    // }




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
