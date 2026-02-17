<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Modules\RideAssignment\app\Models\Ride;
use App\Traits\Upload;
use Modules\UserRolePermission\app\Models\Driver;
use Illuminate\Support\Facades\File;
class DriverController extends Controller
{
    use Upload;
    /**
     * Driver Dashboard
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

        // আজকের rides - rides table থেকে
        $todayRides = Ride::where('driver_id', $driver->id)
            ->whereDate('date', $today)
            ->where('status', '!=', 'cancelled')
            ->whereNull('deleted_at')
            ->with(['rideAssign.subscription.user', 'parent'])
            ->get();

        // সপ্তাহের rides count
        $weeklyRides = Ride::where('driver_id', $driver->id)
            ->whereBetween('date', [$thisWeek, $thisWeek->copy()->addDays(6)])
            ->whereNull('deleted_at')
            ->count();

        // মাসের earnings
        $monthlyEarnings = Ride::where('driver_id', $driver->id)
            ->whereMonth('date', $thisMonth->month)
            ->whereYear('date', $thisMonth->year)
            ->where('status', 'completed')
            ->whereNull('deleted_at')
            ->get()
            ->sum(function($ride) {
                // RideAssign থেকে driver commission নেওয়া
                return $ride->rideAssign ? $ride->rideAssign->driver_commission : 0;
            });

        // Completed rides count (this month)
        $completedRides = Ride::where('driver_id', $driver->id)
            ->whereMonth('date', $thisMonth->month)
            ->whereYear('date', $thisMonth->year)
            ->where('status', 'completed')
            ->whereNull('deleted_at')
            ->count();

        // Pending rides count
        $pendingRides = Ride::where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->whereNull('deleted_at')
            ->count();

        // সাম্প্রতিক rides - শেষ ৭ দিন
        $recentRides = Ride::where('driver_id', $driver->id)
            ->whereBetween('date', [Carbon::now()->subDays(7), Carbon::now()])
            ->whereNull('deleted_at')
            ->with(['rideAssign.subscription.user', 'parent'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Upcoming rides - আগামী ৭ দিন
        $upcomingRides = Ride::where('driver_id', $driver->id)
            ->whereBetween('date', [Carbon::tomorrow(), Carbon::now()->addDays(7)])
            ->whereIn('status', ['pending', 'active'])
            ->whereNull('deleted_at')
            ->with(['rideAssign.subscription.user', 'parent'])
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'driver' => [
                    'name' => $driver->first_name . ' ' . $driver->last_name,
                    'email' => $driver->email,
                    'phone' => $driver->phone,
                    'profile_image' => $driver->profile_image,
                ],
                'stats' => [
                    'today_rides' => $todayRides->count(),
                    'weekly_rides' => $weeklyRides,
                    'monthly_earnings' => number_format($monthlyEarnings, 2),
                    'completed_rides' => $completedRides,
                    'pending_rides' => $pendingRides,
                ],
                'today_rides' => $todayRides->map(function ($ride) {
                    return [
                        'id' => $ride->id,
                        'ride_assign_id' => $ride->ride_assign_id,
                        'ride_type' => $ride->ride_type,
                        'status' => $ride->status,
                        'date' => $ride->date,
                        'pickup' => $ride->pickup,
                        'drop_off' => $ride->drop_off,
                        'pickup_location' => [
                            'id' => $ride->pickupLocation->id ?? null,
                            'address' => $ride->pickupLocation->address ?? null,
                        ],
                        'dropoff_location' => [
                            'id' => $ride->dropoffLocation->id ?? null,
                            'address' => $ride->dropoffLocation->address ?? null,
                        ],
                        'parent' => $ride->parent ? [
                            'name' => $ride->parent->first_name . ' ' . $ride->parent->last_name,
                            'phone' => $ride->parent->phone,
                        ] : null,
                        'ride_assign' => $ride->rideAssign ? [
                            'fare' => $ride->rideAssign->fare,
                            'driver_commission' => $ride->rideAssign->driver_commission,
                            'service_type' => $ride->rideAssign->service_type,
                        ] : null,
                    ];
                }),
                'upcoming_rides' => $upcomingRides->map(function ($ride) {
                    return [
                        'id' => $ride->id,
                        'ride_assign_id' => $ride->ride_assign_id,
                        'ride_type' => $ride->ride_type,
                        'status' => $ride->status,
                        'date' => $ride->date,
                        'pickup' => $ride->pickup,
                        'drop_off' => $ride->drop_off,
                        'parent' => $ride->parent ? [
                            'name' => $ride->parent->first_name . ' ' . $ride->parent->last_name,
                            'phone' => $ride->parent->phone,
                        ] : null,
                    ];
                }),
                'recent_rides' => $recentRides->map(function ($ride) {
                    return [
                        'id' => $ride->id,
                        'ride_assign_id' => $ride->ride_assign_id,
                        'ride_type' => $ride->ride_type,
                        'status' => $ride->status,
                        'date' => $ride->date,
                        'parent' => $ride->parent ?
                            $ride->parent->first_name . ' ' . $ride->parent->last_name :
                            null,
                        'commission' => $ride->rideAssign ?
                            $ride->rideAssign->driver_commission :
                            null,
                        'created_at' => $ride->created_at,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Driver এর সব rides (filtering সহ)
     */
    public function rides(Request $request): JsonResponse
    {
        $driver = $request->user();

        if (!$driver->hasRole('driver')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = Ride::where('driver_id', $driver->id)
            ->whereNull('deleted_at')
            ->with(['rideAssign.subscription.user', 'parent', 'pickupLocation', 'dropoffLocation']);

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Filter by ride type
        if ($request->has('ride_type')) {
            $query->where('ride_type', $request->ride_type);
        }

        $rides = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $rides->map(function ($ride) {
                return [
                    'id' => $ride->id,
                    'ride_assign_id' => $ride->ride_assign_id,
                    'ride_type' => $ride->ride_type,
                    'status' => $ride->status,
                    'date' => $ride->date,
                    'pickup' => $ride->pickup,
                    'drop_off' => $ride->drop_off,
                    'pickup_location' => [
                        'address' => $ride->pickupLocation->address ?? null,
                    ],
                    'dropoff_location' => [
                        'address' => $ride->dropoffLocation->address ?? null,
                    ],
                    'parent' => $ride->parent ? [
                        'name' => $ride->parent->first_name . ' ' . $ride->parent->last_name,
                        'phone' => $ride->parent->phone,
                    ] : null,
                    'ride_assign' => $ride->rideAssign ? [
                        'fare' => $ride->rideAssign->fare,
                        'driver_commission' => $ride->rideAssign->driver_commission,
                        'service_type' => $ride->rideAssign->service_type,
                    ] : null,
                    'created_at' => $ride->created_at,
                ];
            }),
            'pagination' => [
                'total' => $rides->total(),
                'per_page' => $rides->perPage(),
                'current_page' => $rides->currentPage(),
                'last_page' => $rides->lastPage(),
            ],
        ]);
    }

    /**
     * Driver earnings summary
     */
    public function earnings(Request $request): JsonResponse
    {
        $driver = $request->user();

        if (!$driver->hasRole('driver')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // This month earnings
        $thisMonthEarnings = Ride::where('driver_id', $driver->id)
            ->whereMonth('date', $thisMonth->month)
            ->whereYear('date', $thisMonth->year)
            ->where('status', 'completed')
            ->whereNull('deleted_at')
            ->with('rideAssign')
            ->get()
            ->sum(function($ride) {
                return $ride->rideAssign ? $ride->rideAssign->driver_commission : 0;
            });

        // Last month earnings
        $lastMonthEarnings = Ride::where('driver_id', $driver->id)
            ->whereMonth('date', $lastMonth->month)
            ->whereYear('date', $lastMonth->year)
            ->where('status', 'completed')
            ->whereNull('deleted_at')
            ->with('rideAssign')
            ->get()
            ->sum(function($ride) {
                return $ride->rideAssign ? $ride->rideAssign->driver_commission : 0;
            });

        // Total earnings (all time)
        $totalEarnings = Ride::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereNull('deleted_at')
            ->with('rideAssign')
            ->get()
            ->sum(function($ride) {
                return $ride->rideAssign ? $ride->rideAssign->driver_commission : 0;
            });

        return response()->json([
            'success' => true,
            'data' => [
                'this_month' => number_format($thisMonthEarnings, 2),
                'last_month' => number_format($lastMonthEarnings, 2),
                'total' => number_format($totalEarnings, 2),
                'comparison' => [
                    'difference' => number_format($thisMonthEarnings - $lastMonthEarnings, 2),
                    'percentage' => $lastMonthEarnings > 0
                        ? round((($thisMonthEarnings - $lastMonthEarnings) / $lastMonthEarnings) * 100, 2)
                        : 0,
                ],
            ]
        ]);
    }

    public function updateFaceVerification(Request $request)
    {
        $request->validate([
            'face_embedding' => 'required|string',
            'faceImage'      => 'required|string', // base64 string
            'is_verified'    => 'required|numeric',
        ]);

        try {
            $user = $request->user();

            if (!$user->hasRole('driver')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $driver = Driver::where('user_id', $user->id)->first();

            $imageName = '';

            // Decode base64 image and save to public folder
            if ($request->faceImage) {
                $imageData = base64_decode($request->faceImage);
                $imageName = 'face_' . $driver->id . '_' . time() . '.jpg';
                $path = public_path('parent/verification/' . $driver->id);

                // Create directory if not exists
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }

                // Save file
                file_put_contents($path . '/' . $imageName, $imageData);

                // Delete old image if exists
                if ($driver->faceImage && File::exists(public_path($driver->faceImage))) {
                    File::delete(public_path($driver->faceImage));
                }

                $imageName = 'uploads/driver/verification/' . $driver->id . '/' . $imageName;
            }

            $data = [
                'face_embedding' => $request->face_embedding,
                'faceImage'      => $imageName,
                'is_verified'    => $request->is_verified,
            ];

            // Update driver
            $driver->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Face verification data updated successfully',
                'data' => $driver
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update face verification',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
