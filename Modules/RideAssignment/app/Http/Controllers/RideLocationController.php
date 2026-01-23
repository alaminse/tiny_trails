<?php

namespace Modules\RideAssignment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\RideAssignment\app\Models\RideLocation;
use Modules\RideAssignment\app\Models\Ride;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RideLocationController extends Controller
{
    // Create new ride location
    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|integer|exists:rides,id',
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, // ✅ false হবে
                'message' => 'Validation error',
                'errors' => $validator->errors(), // ✅ validator errors পাঠান
            ], 422);
        }
        
        $data = $validator->validated();
        try {
        
            $ride = Ride::find($data['ride_id']);
            
            if (!$ride) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ride not found'
                ], 404);
            }
            
            $responseData = [
                'ride_id'   => $data['ride_id'],
                'driver_id' => $ride->driver_id,
                'parent_id' => $ride->parent_id,
                'kid_id'    => $ride->kid_id,
                'longitude' => $data['longitude'],
                'latitude'  => $data['latitude'],
            ];
            
            // Create new location record
            $rideLocation = RideLocation::create($responseData);

            return response()->json([
                'success' => true,
                'message' => 'Ride location created successfully',
                'data' => $rideLocation,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ride location',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Update ride location
    public function update(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|integer|exists:rides,id',
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, // ✅ false হবে
                'message' => 'Validation error',
                'errors' => $validator->errors(), // ✅ validator errors পাঠান
            ], 422);
        }
        
        $data = $validator->validated();

        try {
            $ride = Ride::find($data['ride_id']);
            
            if (!$ride) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ride not found'
                ], 404);
            }
            
            $rideLocation = RideLocation::where('ride_id', $data['ride_id'])->first();
            
            if (!$rideLocation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ride location not found'
                ], 404);
            }
            
            $rideLocation->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Ride location updated successfully',
                'data' => $rideLocation
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ride location',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete ride location
    public function destroy($id)
    {
        try {
            $rideLocation = RideLocation::find($id);
            
            if (!$rideLocation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ride location not found'
                ], 404);
            }
            
            $rideLocation->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Ride location deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ride location',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // kidsLiveLocation
    // Get ride locations by parent_id
    public function getRides()
    {
        $rideLocations = RideLocation::with(['driver'])
            ->where('parent_id', Auth::id())
            ->get();

        if ($rideLocations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No ride locations found',
                'data'    => [],
            ], 404);
        }

        // ✅ কেবল দরকারি ফিল্ডগুলো নিয়ে নতুন অ্যারে তৈরি
        $data = $rideLocations->map(function ($location) {
            return [
                'id'          => $location->id,
                'ride_id'     => $location->ride_id,
                'driver_name' => $location->driver_name, // accessor থেকে
                'parent_id'   => $location->parent_id,
                'kid_id'      => $location->kid_id,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Ride locations retrieved successfully',
            'data'    => $data,
        ], 200);
    }
    
    // Get ride locations by ride_location_id
    public function getLiveRide(RideLocation $ride_location)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Ride locations retrieved successfully',
                'data' => $ride_location
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ride locations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}