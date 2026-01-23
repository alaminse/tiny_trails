<?php

namespace Modules\RideAssignment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\RideAssignment\app\Models\Ride;

class ApiRideAssignController extends Controller
{
    public function schedule()
    {
        $user = Auth::user();

        // Get rides from today onwards, grouped by date
        $rides = Ride::where('parent_id', $user->id)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->orderBy('pickup')
            ->get()
            ->groupBy('date')
            ->map(function ($dayRides, $date) {
                return [
                    'date' => $date,
                    'day_name' => \Carbon\Carbon::parse($date)->format('l'), // Monday, Tuesday, etc.
                    'formatted_date' => \Carbon\Carbon::parse($date)->format('M d, Y'), // Sep 28, 2025
                    'is_today' => \Carbon\Carbon::parse($date)->isToday(),
                    'is_tomorrow' => \Carbon\Carbon::parse($date)->isTomorrow(),
                    'rides' => $dayRides->map(function ($ride) {
                        return [
                            'id' => $ride->id,
                            'ride_type' => $ride->ride_type,
                            'pickup_time' => \Carbon\Carbon::parse($ride->pickup)->format('h:i A'),
                            'drop_off_time' => \Carbon\Carbon::parse($ride->drop_off)->format('h:i A'),
                            'driver_name' => $ride->driver->name ?? 'N/A',
                            'driver_phone' => $ride->driver->phone ?? null,
                            'status' => $ride->status
                        ];
                    }),
                    'total_rides' => $dayRides->count()
                ];
            })
            ->values(); // Reset array keys

        return response()->json([
            'success' => true,
            'data' => $rides
        ]);
    }

    // Get specific date schedule
    public function getDateSchedule(Request $request)
    {
        $user = Auth::user();
        $date = $request->get('date', now()->format('Y-m-d'));
    
        $rides = Ride::with(['driver', 'kid']) // Eager load relationships
            ->where('parent_id', $user->id)
            ->whereDate('date', $date)
            ->where('date', '>=', now()->format('Y-m-d')) // Only future dates
            ->orderBy('pickup')
            ->get()
            ->map(function ($ride) {
                return [
                    'id' => $ride->id,
                    'ride_type' => $ride->ride_type,
                    'pickup_time' => \Carbon\Carbon::parse($ride->pickup)->format('h:i A'),
                    'drop_off_time' => \Carbon\Carbon::parse($ride->drop_off)->format('h:i A'),
                    'kid_name' => $ride->kid_name ?? 'N/A',
                    'driver_name' => $ride->driver_name ?? 'N/A',
                    'driver_phone' => $ride->driver->phone ?? null,
                    'status' => $ride->status
                ];
            });
    
        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'day_name' => \Carbon\Carbon::parse($date)->format('l'),
                'formatted_date' => \Carbon\Carbon::parse($date)->format('M d, Y'),
                'is_today' => \Carbon\Carbon::parse($date)->isToday(),
                'rides' => $rides,
                'total_rides' => $rides->count()
            ]
        ]);
    }

    // API for getting available dates (calendar view)
    public function getAvailableDates(Request $request)
    {
        $user = Auth::user();

        $availableDates = Ride::whereHas('rideAssign.subscription', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('date', '>=', now()->format('Y-m-d'))
            ->distinct()
            ->pluck('date')
            ->map(function($date) {
                return [
                    'date' => $date,
                    'day_name' => \Carbon\Carbon::parse($date)->format('D'), // Mon, Tue, etc.
                    'day_number' => \Carbon\Carbon::parse($date)->format('d'),
                    'month' => \Carbon\Carbon::parse($date)->format('M'),
                    'year' => \Carbon\Carbon::parse($date)->format('Y'),
                    'is_today' => \Carbon\Carbon::parse($date)->isToday(),
                    'is_weekend' => \Carbon\Carbon::parse($date)->isWeekend()
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $availableDates
        ]);
    }
}
