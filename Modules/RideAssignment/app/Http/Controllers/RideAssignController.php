<?php

namespace Modules\RideAssignment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\RideAssignment\app\Models\Ride;
use Modules\RideAssignment\app\Models\RideAssign;
use Modules\Subscription\app\Models\Location;
use Modules\Subscription\app\Models\Subscription;
use Modules\UserRolePermission\app\Models\Driver;
use Modules\UserRolePermission\app\Models\Kid;

class RideAssignController extends Controller
{
    public function index()
    {
        return view('rideassignment::index');
    }

    public function subscriptions()
    {
        return view('rideassignment::subscriptions');
    }

    public function getSubscriptions()
    {
        $subscriptions = Subscription::where('assign_ride', 'unassigned')->with([
            'pickupLocation',
            'dropoffLocation',
            'plan',
            'kid',
            'user'
        ])->get();

        $html = view('rideassignment::partials.subscription', compact('subscriptions'))->render();

        return response()->json(['html' => $html]);
    }

    public function create(Subscription $subscription)
    {
        // Only get this specific subscription if it's unassigned and active
        $subscription->load(['user', 'kid', 'pickupLocation', 'dropoffLocation', 'plan']);

        // Available drivers get
        $drivers = Driver::with('user')->where('status', 'active')->get();

        return view('rideassignment::create', compact('subscription', 'drivers'));
    }

    // Store function - For new ride assign
    public function store(Request $request, Subscription $subscription)
    {
        // Validation rules
        $validated = $request->validate([
            'selected_dates'            => 'required|json',
            'base_fare'                 => 'required|numeric|min:0',
            'working_days'              => 'required|numeric|min:0',
            'driver_commission_percent' => 'required|numeric|min:0|max:100',
            'platform_fee'              => 'required|numeric|min:0|max:100',
            'driver'                    => 'required|array',
            'driver.*'                  => 'required|exists:drivers,id',
            'ride_date'                 => 'required|array',
            'ride_date.*'               => 'required|date|after_or_equal:today',
            'pickup_time'               => 'required|array',
            'pickup_time.*'             => 'required|date_format:H:i',
            'dropoff_time'              => 'required|array',
            'dropoff_time.*'            => 'required|date_format:H:i|after:pickup_time.*',
            'return_pickup_time'        => 'required|array',
            'return_pickup_time.*'      => 'required|date_format:H:i|after:dropoff_time.*',
            'return_home_time'          => 'required|array',
            'return_home_time.*'        => 'required|date_format:H:i|after:return_pickup_time.*',
        ], [
            // Custom error messages
            'selected_dates.required'               => 'Please select at least one date.',
            'selected_dates.json'                   => 'Invalid date format.',
            'base_fare.required'                    => 'Base fare is required.',
            'base_fare.numeric'                     => 'Base fare must be a valid number.',
            'base_fare.min'                         => 'Base fare must be greater than 0.',
            'driver_commission_percent.required'    => 'Driver commission percentage is required.',
            'driver_commission_percent.max'         => 'Driver commission cannot exceed 100%.',
            'platform_fee.max'                      => 'Platform fee cannot exceed 100%.',
            'driver.*.exists'                       => 'Selected driver does not exist.',
            'ride_date.*.after_or_equal'            => 'Ride date cannot be in the past.',
            'pickup_time.*.date_format'             => 'Pickup time must be in HH:MM format.',
            'dropoff_time.*.after'                  => 'Drop off time must be after pickup time.',
            'return_pickup_time.*.after'            => 'Return pickup time must be after drop off time.',
            'return_home_time.*.after'              => 'Return home time must be after return pickup time.',
        ]);

        // Additional custom validations
        $selectedDates = json_decode($validated['selected_dates']);

        // Validate that all required arrays have entries for each selected date
        foreach ($selectedDates as $date) {
            if (!isset($validated['driver'][$date])) {
                return response()->json([
                    'success' => false,
                    'message' => "Driver not assigned for date: {$date}"
                ], 422);
            }

            if (!isset($validated['pickup_time'][$date])) {
                return response()->json([
                    'success' => false,
                    'message' => "Pickup time not set for date: {$date}"
                ], 422);
            }

            if (!isset($validated['dropoff_time'][$date])) {
                return response()->json([
                    'success' => false,
                    'message' => "Drop off time not set for date: {$date}"
                ], 422);
            }

            if (!isset($validated['return_pickup_time'][$date])) {
                return response()->json([
                    'success' => false,
                    'message' => "Return pickup time not set for date: {$date}"
                ], 422);
            }

            if (!isset($validated['return_home_time'][$date])) {
                return response()->json([
                    'success' => false,
                    'message' => "Return home time not set for date: {$date}"
                ], 422);
            }
        }

        // Validate that commission percentages don't exceed 100%
        if (($validated['driver_commission_percent'] + $validated['platform_fee']) > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Driver commission and platform fee combined cannot exceed 100%.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $baseFare = $validated['base_fare'];
            $driverCommissionPercent = $validated['driver_commission_percent'];
            $platformFee = $validated['platform_fee'];

            // Calculate commissions
            $driverCommission = ($baseFare * $driverCommissionPercent) / 100;
            $platformCommission = ($baseFare * $platformFee) / 100;

            // Create ride assignment
            $rideAssign = RideAssign::create([
                'subscription_id'       => $subscription->id ?? 1,
                'parent_id'             => $subscription->user_id ?? 0,
                'fare'                  => $baseFare,
                'driver_commission'     => $driverCommission,
                'platform_commission'   => $platformCommission,
                'service_type'          => count($selectedDates) > 1 ? 'multiple_day' : 'single_day',
                'selected_dates'        => $validated['selected_dates'],
                'total_days'            => $validated['working_days'],
                'status'                => 'active'
            ]);

            $ride_wise_commission = ($driverCommission / $validated['working_days'] ) / 2;
            // Create rides for each selected date
            foreach ($selectedDates as $date) {
                $driver = Driver::find($validated['driver'][$date]);
                $driverId = $driver->user_id;
                // Define ride types with their corresponding time fields
                $rideTypes = [
                    'pickup' => [
                        'pickup_time'           => $validated['pickup_time'][$date],
                        'drop_off_time'         => $validated['dropoff_time'][$date],
                        'pickup_location_id'    => $subscription->pickup_location_id,
                        'dropoff_location_id'   => $subscription->dropoff_location_id
                    ],
                    'return_home' => [
                        'pickup_time'   => $validated['return_pickup_time'][$date],
                        'drop_off_time' => $validated['return_home_time'][$date],
                        'pickup_location_id'    => $subscription->dropoff_location_id,
                        'dropoff_location_id'   => $subscription->pickup_location_id
                    ]
                ];

                // Create rides for each type
                foreach ($rideTypes as $rideType => $times) {
                    Ride::create([
                        'ride_assign_id'        => $rideAssign->id,
                        'driver_id'             => $driverId,
                        'parent_id'             => $subscription->user_id,
                        'pickup_location_id'    => $times['pickup_location_id'],
                        'dropoff_location_id'   => $times['dropoff_location_id'],
                        'ride_type'             => $rideType,
                        'commission'            => $ride_wise_commission,
                        'date'                  => $date,
                        'pickup'                => $times['pickup_time'],
                        'drop_off'              => $times['drop_off_time'],
                        'status'                => 'assigned'
                    ]);
                }
            }

            $subscription->update(['assign_ride' => 'assigned']);
            DB::commit();

            return redirect()->route('admin.ride.assign.index')->with('success', 'Rides assigned successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error assigning rides: ' . $e->getMessage());
        }
    }

    // API endpoints for AJAX calls
    public function getKidInfo($id)
    {
        $kid = Kid::find($id);
        if (!$kid) {
            return response()->json(['error' => 'Kid not found'], 404);
        }

        return response()->json([
            'id' => $kid->id,
            'name' => $kid->name,
            'age' => $kid->age,
        ]);
    }

    public function getLocationInfo($id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json(['error' => 'Location not found'], 404);
        }

        return response()->json([
            'id' => $location->id,
            'address' => $location->address,
            'city' => $location->city,
            'state' => $location->state,
        ]);
    }

    public function getData()
    {
        $rides = RideAssign::latest()->get();

        $html = view('rideassignment::partials.table_rows', compact('rides'))->render();

        return response()->json(['html' => $html]);
    }
}
