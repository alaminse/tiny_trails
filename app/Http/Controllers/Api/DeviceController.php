<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceLocation;
use App\Services\TrackSolidProService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    protected $trackSolidService;

    public function __construct(TrackSolidProService $trackSolidService)
    {
        $this->trackSolidService = $trackSolidService;
    }

    /**
     * Get all devices for a specific kid
     */
    public function getDevicesForKid($kidId)
    {
        try {
            $devices = Device::where('kid_id', $kidId)
                ->with('lastLocation')
                ->orderBy('created_at', 'desc')
                ->get();

            // Update device status from TrackSolidPro
            foreach ($devices as $device) {
                $this->updateDeviceStatus($device);
            }

                return response()->json([
                'success' => true,
                'data' => $devices->map(function ($device) {
                    return [
                        'id' => (int) $device->id,
                        'kid_id' => (int) $device->kid_id,
                        'device_name' => $device->device_name,
                        'imei' => $device->imei,
                        'device_type' => $device->device_type,
                        'phone_number' => $device->phone_number,
                        'is_active' => (bool) $device->is_active,
                        'is_online' => (bool) $device->is_online,
                        'battery_level' => $device->battery_level ? (int) $device->battery_level : null,
                        'signal_strength' => $device->signal_strength ? (int) $device->signal_strength : null,
                        'last_location' => $device->lastLocation ? [
                            'latitude' => (float) $device->lastLocation->latitude,
                            'longitude' => (float) $device->lastLocation->longitude,
                            'address' => $device->lastLocation->address,
                            'accuracy' => $device->lastLocation->accuracy ? (float) $device->lastLocation->accuracy : null,
                            'speed' => $device->lastLocation->speed ? (float) $device->lastLocation->speed : null,
                            'timestamp' => $device->lastLocation->timestamp,
                        ] : null,
                        'last_update_time' => $device->last_update_time,
                        'created_at' => $device->created_at,
                        'updated_at' => $device->updated_at,
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching devices for kid: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ডিভাইস তথ্য পেতে সমস্যা হয়েছে'
            ], 500);
        }
    }

    /**
     * Update device information
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'device_name' => 'sometimes|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ভুল তথ্য প্রদান করা হয়েছে',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $device = Device::findOrFail($id);
            $device->update($request->only(['device_name', 'phone_number', 'is_active']));

            return response()->json([
                'success' => true,
                'message' => 'ডিভাইস তথ্য আপডেট করা হয়েছে',
                'data' => $device
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error updating device: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ডিভাইস আপডেট করতে সমস্যা হয়েছে'
            ], 500);
        }
    }

    /**
     * Delete a device
     */
    public function destroy($id)
    {
        try {
            $device = Device::findOrFail($id);

            // Stop tracking in TrackSolidPro
            $this->trackSolidService->stopTracking($device->imei);

            // Delete related location records
            DeviceLocation::where('device_id', $device->id)->delete();

            // Delete device
            $device->delete();

            return response()->json([
                'success' => true,
                'message' => 'ডিভাইস সফলভাবে ডিলিট করা হয়েছে'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting device: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ডিভাইস ডিলিট করতে সমস্যা হয়েছে'
            ], 500);
        }
    }

    /**
     * Get device status
     */
    public function getDeviceStatus($id)
    {
        try {
            $device = Device::findOrFail($id);
            $this->updateDeviceStatus($device);

            return response()->json([
                'success' => true,
                'data' => [
                    'is_online' => $device->is_online,
                    'battery_level' => $device->battery_level,
                    'signal_strength' => $device->signal_strength,
                    'last_location' => $device->lastLocation,
                    'last_update_time' => $device->last_update_time,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error getting device status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ডিভাইস স্ট্যাটাস পেতে সমস্যা হয়েছে'
            ], 500);
        }
    }

    /**
     * Start device tracking
     */
    public function startTracking(Request $request, $id)
    {
        try {
            $device = Device::findOrFail($id);

            $result = $this->trackSolidService->startTracking($device->imei);

            if ($result) {
                $device->update(['is_active' => true]);

                return response()->json([
                    'success' => true,
                    'message' => 'ট্র্যাকিং শুরু করা হয়েছে'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'ট্র্যাকিং শুরু করতে সমস্যা হয়েছে'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error starting tracking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ট্র্যাকিং শুরু করতে সমস্যা হয়েছে'
            ], 500);
        }
    }

    /**
     * Get location history
     */
    public function getLocationHistory(Request $request, $id)
    {
        try {
            $device = Device::findOrFail($id);

            $startDate = $request->input('start_date', now()->subDays(7));
            $endDate = $request->input('end_date', now());

            $locations = DeviceLocation::where('device_id', $device->id)
                ->whereBetween('timestamp', [$startDate, $endDate])
                ->orderBy('timestamp', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $locations
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error getting location history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'অবস্থান ইতিহাস পেতে সমস্যা হয়েছে'
            ], 500);
        }
    }

    /**
     * Send command to device
     */
    public function sendCommand(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'command' => 'required|string',
            'params' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ভুল কমান্ড প্রদান করা হয়েছে',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $device = Device::findOrFail($id);

            $result = $this->trackSolidService->sendCommand(
                $device->imei,
                $request->command,
                $request->params ?? []
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'কমান্ড সফলভাবে পাঠানো হয়েছে'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'কমান্ড পাঠাতে সমস্যা হয়েছে'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error sending command: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'কমান্ড পাঠাতে সমস্যা হয়েছে'
            ], 500);
        }
    }

    /**
     * Update device status from TrackSolidPro
     */
    private function updateDeviceStatus($device)
    {
        try {
            $status = $this->trackSolidService->getDeviceStatus($device->imei);

            if ($status) {
                $device->update([
                    'is_online' => $status['is_online'] ?? false,
                    'battery_level' => $status['battery_level'] ?? null,
                    'signal_strength' => $status['signal_strength'] ?? null,
                    'last_update_time' => now(),
                ]);

                // Update location if available
                if (isset($status['location'])) {
                    $this->updateDeviceLocation($device, $status['location']);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error updating device status: ' . $e->getMessage());
        }
    }

    /**
     * Update device location
     */
    private function updateDeviceLocation($device, $locationData)
    {
        try {
            DeviceLocation::create([
                'device_id' => $device->id,
                'latitude' => $locationData['latitude'],
                'longitude' => $locationData['longitude'],
                'address' => $locationData['address'] ?? null,
                'accuracy' => $locationData['accuracy'] ?? null,
                'speed' => $locationData['speed'] ?? null,
                'timestamp' => $locationData['timestamp'] ?? now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating device location: ' . $e->getMessage());
        }
    }

     /**
     * Add a new device - Modified to allow adding without TrackSolid validation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kid_id' => 'required|exists:kids,id',
            'device_name' => 'required|string|max:255',
            'imei' => 'required|string|size:15|unique:devices,imei',
            'device_type' => 'required|in:watch,phone,tracker',
            'phone_number' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create device in database first (without TrackSolidPro validation)
            $device = Device::create([
                'kid_id' => $request->kid_id,
                'device_name' => $request->device_name,
                'imei' => $request->imei,
                'device_type' => $request->device_type,
                'phone_number' => $request->phone_number,
                'is_active' => true,
                'is_online' => false, // Will be updated when TrackSolid connects
                'tracksolid_device_id' => null, // Will be updated when found
            ]);

            // Try to connect to TrackSolidPro in background (non-blocking)
            $this->tryConnectToTrackSolid($device);

            return response()->json([
                'success' => true,
                'message' => 'Device added successfully. We will try to connect to TrackSolidPro in the background.',
                'data' => [
                    'id' => $device->id,
                    'kid_id' => $device->kid_id,
                    'device_name' => $device->device_name,
                    'imei' => $device->imei,
                    'device_type' => $device->device_type,
                    'phone_number' => $device->phone_number,
                    'is_active' => $device->is_active,
                    'is_online' => $device->is_online,
                    'battery_level' => $device->battery_level,
                    'signal_strength' => $device->signal_strength,
                    'created_at' => $device->created_at,
                    'updated_at' => $device->updated_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error adding device: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add device'
            ], 500);
        }
    }

    /**
     * Try to connect device to TrackSolidPro (non-blocking)
     */
    private function tryConnectToTrackSolid($device)
    {
        try {
            // Try to get device info from TrackSolidPro
            $deviceInfo = $this->trackSolidService->getDeviceInfo($device->imei);

            if ($deviceInfo) {
                // Update device with TrackSolid info
                $device->update([
                    'tracksolid_device_id' => $deviceInfo['device_id'] ?? null,
                    'is_online' => true,
                ]);

                // Start tracking
                $this->trackSolidService->startTracking($device->imei);

                // Get initial status
                $this->updateDeviceStatus($device);

                Log::info("Device {$device->imei} connected to TrackSolidPro successfully");
            } else {
                Log::warning("Device {$device->imei} not found in TrackSolidPro, but saved in database");
            }
        } catch (\Exception $e) {
            Log::error("Failed to connect device {$device->imei} to TrackSolidPro: " . $e->getMessage());
        }
    }

    /**
     * Manual sync with TrackSolidPro
     */
    public function syncWithTrackSolid($id)
    {
        try {
            $device = Device::findOrFail($id);

            $deviceInfo = $this->trackSolidService->getDeviceInfo($device->imei);

            if ($deviceInfo) {
                $device->update([
                    'tracksolid_device_id' => $deviceInfo['device_id'] ?? null,
                    'is_online' => true,
                ]);

                $this->trackSolidService->startTracking($device->imei);
                $this->updateDeviceStatus($device);

                return response()->json([
                    'success' => true,
                    'message' => 'Device synced with TrackSolidPro successfully'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found in TrackSolidPro system'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Error syncing device: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync device'
            ], 500);
        }
    }
}
