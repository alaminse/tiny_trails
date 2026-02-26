<?php

namespace Modules\RideAssignment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DriverCapacityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\RideAssignment\app\Models\Ride;
use Modules\RideAssignment\app\Models\RideAssign;
use Modules\Subscription\app\Models\Location;
use Modules\Subscription\app\Models\Subscription;
use Modules\UserRolePermission\app\Models\Driver;
use Modules\UserRolePermission\app\Models\Kid;

class RideAssignController extends Controller
{
    protected DriverCapacityService $capacityService;

    public function __construct(DriverCapacityService $capacityService)
    {
        $this->capacityService = $capacityService;
    }

    public function store(Request $request, int $subscriptionId)
    {
        return 'okk';
        $request->validate([
            'selected_dates' => 'required|json',
            'driver' => 'required|array',
            'ride_date' => 'required|array',
            'pickup_time' => 'required|array',
            'dropoff_time' => 'required|array',
        ]);

        $selectedDates = json_decode($request->selected_dates, true);

        // ✅ Capacity check BEFORE saving anything
        $errors = [];

        foreach ($selectedDates as $date) {
            $driverId = $request->input("driver.{$date}");
            $pickupTime = $request->input("pickup_time.{$date}");

            if (! $driverId) {
                continue;
            }

            // Check capacity
            if (! $this->capacityService->isDriverAvailable($driverId, $date, $pickupTime)) {
                $info = $this->capacityService->getCapacityInfo($driverId, $date, $pickupTime);
                $errors[] = "Date {$date}: Driver is at full capacity ({$info['message']})";
            }

            // Check time buffer
            if (! $this->capacityService->hasTimeBuffer($driverId, $date, $pickupTime)) {
                $errors[] = "Date {$date}: Driver needs at least 10 min buffer after previous drop-off.";
            }
        }

        // ✅ If any capacity errors, return back with errors
        if (! empty($errors)) {
            return back()
                ->withErrors($errors)
                ->withInput();
        }

        // ✅ All good — save RideAssign
        $subscription = Subscription::findOrFail($subscriptionId);

        $rideAssign = RideAssign::create([
            'subscription_id' => $subscriptionId,
            'fare' => $subscription->kid_wage?->sell_price ?? $subscription->plan->sell_price,
            'service_type' => count($selectedDates) > 1
                        ? 'multiple_day'
                        : 'single_day',
            'total_days' => count($selectedDates),
            'selected_dates' => $request->selected_dates,
            'status' => 'active',
        ]);

        // ✅ Create individual Ride records per date
        foreach ($selectedDates as $date) {
            $driverId = $request->input("driver.{$date}");

            if (! $driverId) {
                continue;
            }

            Ride::create([
                'ride_assign_id' => $rideAssign->id,
                'driver_id' => $driverId,
                'parent_id' => $subscription->user_id,
                'pickup_location_id' => $subscription->pickup_location_id,
                'dropoff_location_id' => $subscription->dropoff_location_id,
                'ride_type' => 'morning',
                'date' => $date,
                'pickup' => $request->input("pickup_time.{$date}"),
                'drop_off' => $request->input("dropoff_time.{$date}"),
                'status' => 'assigned',
            ]);

            // Return ride (afternoon)
            Ride::create([
                'ride_assign_id' => $rideAssign->id,
                'driver_id' => $driverId,
                'parent_id' => $subscription->user_id,
                'pickup_location_id' => $subscription->dropoff_location_id, // reversed
                'dropoff_location_id' => $subscription->pickup_location_id,  // reversed
                'ride_type' => 'afternoon',
                'date' => $date,
                'pickup' => $request->input("return_pickup_time.{$date}"),
                'drop_off' => $request->input("return_home_time.{$date}"),
                'status' => 'assigned',
            ]);
        }

        // ✅ Mark subscription as assigned
        $subscription->update(['assign_ride' => 'assigned']);

        return redirect()
            ->route('admin.ride.assign.index')
            ->with('success', 'Ride assignments created successfully!');
    }

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
            'user',
        ])->get();

        $html = view('rideassignment::partials.subscription', compact('subscriptions'))->render();

        return response()->json(['html' => $html]);
    }

    public function create(Subscription $subscription)
    {
        // Only get this specific subscription if it's unassigned and active
        $subscription->load(['user', 'kid', 'kid_wage', 'pickupLocation', 'dropoffLocation', 'plan']);

        // Available drivers get
        $drivers = Driver::with('user')->where('status', 'active')->get();

        return view('rideassignment::create', compact('subscription', 'drivers'));
    }

    // API endpoints for AJAX calls
    public function getKidInfo($id)
    {
        $kid = Kid::find($id);
        if (! $kid) {
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
        if (! $location) {
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

    // ═══════════════════════════════════════════════════════
    // METHOD 1 — rideAssignStore
    // Route: POST ride-assign/{subscription}/store
    // ═══════════════════════════════════════════════════════
    public function rideAssignStore(Request $request, $subscriptionId)
    {
        // ✅ driver field নেই — পরে Shift এ assign হবে
        $request->validate([
            'selected_dates'     => 'required|json',
            'pickup_time'        => 'required|array',
            'dropoff_time'       => 'required|array',
            'return_pickup_time' => 'nullable|array',
            'return_home_time'   => 'nullable|array',
            'fare'               => 'nullable|numeric',
        ]);

        $selectedDates = json_decode($request->selected_dates, true);

        if (empty($selectedDates)) {
            return back()
                ->withErrors(['selected_dates' => 'Please select at least one date.'])
                ->withInput();
        }

        // rides.ride_type enum('pickup', 'return_home')
        $morningRideType   = 'pickup';
        $afternoonRideType = 'return_home';

        try {
            DB::transaction(function () use (
                $request,
                $subscriptionId,
                $selectedDates,
                $morningRideType,
                $afternoonRideType
            ) {
                $subscription = Subscription::with(['kid_wage', 'plan'])
                    ->findOrFail($subscriptionId);

                $rideAssign = RideAssign::create([
                    'subscription_id' => $subscription->id,
                    'fare'            => $subscription->kid_wage?->sell_price
                                         ?? $subscription->plan?->sell_price,
                    'service_type'    => count($selectedDates) > 1
                                         ? 'multiple_day'
                                         : 'single_day',
                    'total_days'      => count($selectedDates),
                    'selected_dates'  => json_encode($selectedDates),
                    'status'          => 'active',
                ]);

                foreach ($selectedDates as $date) {
                    // ✅ Morning ride — driver_id null, Shift এ assign হবে
                    Ride::create([
                        'ride_assign_id'      => $rideAssign->id,
                        'parent_id'           => $subscription->user_id,
                        'pickup_location_id'  => $subscription->pickup_location_id,
                        'dropoff_location_id' => $subscription->dropoff_location_id,
                        'ride_type'           => $morningRideType,
                        'date'                => $date,
                        'pickup'              => $request->input("pickup_time.{$date}"),
                        'drop_off'            => $request->input("dropoff_time.{$date}"),
                    ]);

                    // ✅ Afternoon ride — locations reversed
                    Ride::create([
                        'ride_assign_id'      => $rideAssign->id,
                        'parent_id'           => $subscription->user_id,
                        'pickup_location_id'  => $subscription->dropoff_location_id,
                        'dropoff_location_id' => $subscription->pickup_location_id,
                        'ride_type'           => $afternoonRideType,
                        'date'                => $date,
                        'pickup'              => $request->input("return_pickup_time.{$date}"),
                        'drop_off'            => $request->input("return_home_time.{$date}"),
                    ]);
                }

                $subscription->update(['assign_ride' => 'assigned']);
            });

            return redirect()
                ->route('admin.ride.assign.index')
                ->with('success', count($selectedDates) . ' day(s) assigned! Now add to a Driver Shift.');

        } catch (\Exception $e) {
            Log::error('RideAssign store failed: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // ═══════════════════════════════════════════════════════
    // METHOD 2 — checkCapacity
    // Route: POST ride-assign/check-capacity  (AJAX)
    // ═══════════════════════════════════════════════════════
    public function checkCapacity(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|integer|exists:drivers,id',
            'date' => 'required|date',
            'pickup_time' => 'required|string',
        ]);

        $capacityService = app(DriverCapacityService::class);

        $info = $capacityService->getCapacityInfo(
            $request->driver_id,
            $request->date,
            $request->pickup_time
        );

        $hasBuffer = $capacityService->hasTimeBuffer(
            $request->driver_id,
            $request->date,
            $request->pickup_time
        );

        return response()->json([
            'capacity' => $info,
            'has_buffer' => $hasBuffer,
            'available' => $info['available'] && $hasBuffer,
        ]);
    }
}
