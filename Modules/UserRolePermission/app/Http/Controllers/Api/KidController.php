<?php

namespace Modules\UserRolePermission\app\Http\Controllers\Api;

use Exception;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Subscription\app\Models\Location;
use Modules\UserRolePermission\app\Models\Kid;
use Modules\UserRolePermission\app\Http\Requests\KidRequest;
use Modules\UserRolePermission\app\Http\Resources\KidResource;
use Modules\UserRolePermission\app\Repositories\KidRepository;

class KidController extends Controller
{
    use Upload;

    protected $kidRepository;

    public function __construct(KidRepository $kidRepository)
    {
        $this->kidRepository = $kidRepository;
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('kids');

        return response()->json(
            KidResource::collection($user->kids),
            200
        );
    }

    /**
     * Helper function to create or update a location.
     * This avoids code duplication.
     */
    private function createOrUpdateLocation(array $data, string $type): Location
    {
        $locationField = $type . '_location';
        $latField = $type . '_latitude';
        $lngField = $type . '_longitude';
        $street1Field = $type . '_street1';
        $street2Field = $type . '_street2';
        $cityField = $type . '_city';
        $stateField = $type . '_state';
        $postalField = $type . '_postal_code';
        $countryField = $type . '_country_code';

        return Location::updateOrCreate(
            [
                'address' => $data[$locationField],
                'type' => $type,
            ],
            [
                'address' => $data[$locationField],
                // FIX: Explicitly cast to float to prevent decimal casting errors
                'latitude' => (float) $data[$latField],
                'longitude' => (float) $data[$lngField],
                'street1' => !empty($data[$street1Field]) ? trim($data[$street1Field]) : trim($data[$locationField]),
                'street2' => !empty($data[$street2Field]) ? trim($data[$street2Field]) : null,
                'city' => !empty($data[$cityField]) ? trim($data[$cityField]) : null,
                'state' => !empty($data[$stateField]) ? trim($data[$stateField]) : null,
                'postal_code' => !empty($data[$postalField]) ? trim($data[$postalField]) : null,
                'country_code' => !empty($data[$countryField]) ? trim($data[$countryField]) : 'AU',
                'type' => $type,
            ]
        );
    }

    public function store(KidRequest $request)
    {
        try {

            Log::info('Kid create request start');

            $data = $request->validated();
            Log::info('Validated Data', $data);

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $data['user_id'] = $user->id;

            Log::info('User ID', ['user_id' => $user->id]);

            if (isset($data['emergency_contacts']) && is_array($data['emergency_contacts'])) {
                $data['emergency_contacts'] = json_encode($data['emergency_contacts']);
            }

            // Validate coordinates
            if (!isset($data['pickup_latitude']) || !isset($data['pickup_longitude'])) {
                return response()->json(['message' => 'Pickup location coordinates are required.'], 422);
            }

            if (!isset($data['dropoff_latitude']) || !isset($data['dropoff_longitude'])) {
                return response()->json(['message' => 'Dropoff location coordinates are required.'], 422);
            }

            Log::info('Coordinates', [
                'pickup_lat' => $data['pickup_latitude'],
                'pickup_lng' => $data['pickup_longitude'],
                'drop_lat' => $data['dropoff_latitude'],
                'drop_lng' => $data['dropoff_longitude']
            ]);

            // Create pickup location
            $pickupLocation = $this->createOrUpdateLocation($data, 'pickup');
            Log::info('Pickup Location', $pickupLocation->toArray());

            $data['pickup_location_id'] = $pickupLocation->id;

            // Create dropoff location
            $dropoffLocation = $this->createOrUpdateLocation($data, 'dropoff');
            Log::info('Dropoff Location', $dropoffLocation->toArray());

            $data['dropoff_location_id'] = $dropoffLocation->id;

            // Calculate distance
            $distance = $this->calculateDistance(
                $pickupLocation->latitude,
                $pickupLocation->longitude,
                $dropoffLocation->latitude,
                $dropoffLocation->longitude
            );

            Log::info('Calculated Distance Raw', ['distance' => $distance]);

            $data['distance_between_locations'] = round($distance, 2);

            Log::info('Distance After Round', [
                'distance_between_locations' => $data['distance_between_locations']
            ]);

            Log::info('Final Data Before Save', $data);

            $kid = $this->kidRepository->create($data);

            Log::info('Kid Created', $kid->toArray());

            return response()->json(new KidResource($kid), 201);

        } catch (Exception $e) {

            Log::error('Kid creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to create kid',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Kid $kid)
    {
        $kid->load('parent', 'pickupLocation', 'dropoffLocation');

        return response()->json(new KidResource($kid));
    }

    public function update(KidRequest $request, Kid $kid)
    {
        try {
            $data = $request->validated();
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $data['user_id'] = $user->id;

            if (isset($data['emergency_contacts']) && is_array($data['emergency_contacts'])) {
                $data['emergency_contacts'] = json_encode($data['emergency_contacts']);
            }

            // Update locations if they are provided
            if (isset($data['pickup_location']) && !empty($data['pickup_location'])) {
                if (!isset($data['pickup_latitude']) || !isset($data['pickup_longitude'])) {
                    return response()->json(['message' => 'Pickup location coordinates are required.'], 422);
                }
                $pickupLocation = $this->createOrUpdateLocation($data, 'pickup');
                $data['pickup_location_id'] = $pickupLocation->id;
            }

            if (isset($data['dropoff_location']) && !empty($data['dropoff_location'])) {
                if (!isset($data['dropoff_latitude']) || !isset($data['dropoff_longitude'])) {
                    return response()->json(['message' => 'Dropoff location coordinates are required.'], 422);
                }
                $dropoffLocation = $this->createOrUpdateLocation($data, 'dropoff');
                $data['dropoff_location_id'] = $dropoffLocation->id;
            }

            // Calculate distance if both locations are available
            if (isset($data['pickup_location_id']) && isset($data['dropoff_location_id'])) {
                $pickupLoc = Location::find($data['pickup_location_id']);
                $dropoffLoc = Location::find($data['dropoff_location_id']);
                if ($pickupLoc && $dropoffLoc) {
                    $data['distance_between_locations'] = $this->calculateDistance(
                        $pickupLoc->latitude,
                        $pickupLoc->longitude,
                        $dropoffLoc->latitude,
                        $dropoffLoc->longitude
                    );
                }
            }

            $kid = $this->kidRepository->update($kid->id, $data);

            return response()->json(new KidResource($kid), 200);

        } catch (Exception $e) {
            Log::error('Kid update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update kid', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper function to calculate distance.
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));

        return round($earthRadius * $c, 2);
    }

    public function show(Kid $kid)
    {
        return response()->json(new KidResource($kid));
    }

    public function destroy(Kid $kid)
    {
        try {
            $this->kidRepository->delete($kid->id);
            return response()->json(['message' => 'Kid deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete kid.', 'error' => $e->getMessage()], 500);
        }
    }


    public function ridePricing(Kid $kid)
    {
        try {
            if ($kid->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 403);
            }

            $data = $this->kidRepository->ridePricing($kid->id);

            return response()->json([
                'success' => true,        // ← add success flag
                'message' => 'Kid ride pricing info.',
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something is wrong. Try Again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function respondToWage(Request $request, Kid $kid)
    {
        try {
            if ($kid->user_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }

            $request->validate([
                'action'     => 'required|in:accept,reject',
                'start_date' => 'required_if:action,accept|nullable|date|after_or_equal:today',
                'end_date'   => 'required_if:action,accept|nullable|date|after:start_date',
            ]);

            $pendingWage = $kid->pendingWage;

            if (!$pendingWage) {
                return response()->json(['success' => false, 'message' => 'No pending wage found.'], 404);
            }

            if ($pendingWage->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'This pricing has already been responded to.'], 422);
            }

            $pendingWage->update([
                'status'     => $request->action === 'accept' ? 'active' : 'inactive',
                'start_date' => $request->action === 'accept' ? $request->start_date : $pendingWage->start_date,
                'end_date'   => $request->action === 'accept' ? $request->end_date : $pendingWage->end_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => $request->action === 'accept'
                    ? 'Pricing accepted successfully.'
                    : 'Pricing rejected.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
