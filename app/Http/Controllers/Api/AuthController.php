<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\UserRolePermission\app\Http\Resources\UserResource;
use Modules\UserRolePermission\app\Models\Driver;
use App\Traits\Upload;

class AuthController extends Controller
{

    use Upload;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }


        $user = Auth::user();

        // Check if user is active
        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is ' . $user->status
            ], 403);
        }

        // Update FCM token if provided
        if ($request->fcm_token) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'role'  => $user->getRoleNames(),
                'token_type' => 'Bearer'
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'role'  => $request->user()->getRoleNames()
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Here you would typically send a password reset email
        // For now, we'll just return a success message

        return response()->json([
            'success' => true,
            'message' => 'Password reset link sent to your email'
        ]);
    }

    public function profile(Request $request)
    {
        try {
            $user = Auth::user();

            // Load relationships
            $user->load(['country', 'state', 'city']);

            // If user is a driver, load driver relationship
            if ($user->hasRole('driver')) {
                $user->loadMissing('driver');
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile retrieved successfully',
                'data' => new UserResource($user)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile (Parent or Driver)
     *
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $isDriver = $user->hasRole('driver');
            $driver = $isDriver ? Driver::where('user_id', $user->id)->first() : null;

            // Update user basic information
            $userData = $request->only([
                'first_name', 'last_name', 'email', 'phone', 'dob',
                'gender', 'height_cm', 'weight_kg', 'address',
                'country_id', 'state_id', 'city_id'
            ]);

            // Upload new profile photo if provided
            if ($request->file('photo')) {
                $userData['photo'] = $this->uploadFile($request->file('photo'), 'user');
                if ($user->photo) {
                    $this->deleteFile($user->photo);
                }
            }

            // Handle password update
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Update driver-specific information if user is a driver
            if ($isDriver && $driver && $request->hasAny([
                'driving_license_number', 'driving_license_expiry', 'driving_license_image',
                'car_model', 'car_make', 'car_year', 'car_color', 'car_plate_number',
                'car_image', 'face_image'
            ])) {
                $driverData = $request->only([
                    'driving_license_number', 'driving_license_expiry',
                    'car_model', 'car_make', 'car_year', 'car_color', 'car_plate_number'
                ]);

                // Upload driving license image if provided
                if ($request->file('driving_license_image')) {
                    $driverData['driving_license_image'] = $this->uploadFile($request->file('driving_license_image'), 'driver/license');
                    if ($driver->driving_license_image) {
                        $this->deleteFile($driver->driving_license_image);
                    }
                }

                // Upload car image if provided
                if ($request->file('car_image')) {
                    $driverData['car_image'] = $this->uploadFile($request->file('car_image'), 'driver/car');
                    if ($driver->car_image) {
                        $this->deleteFile($driver->car_image);
                    }
                }

                // Upload face image if provided
                if ($request->file('face_image')) {
                    $driverData['faceImage'] = $this->uploadFile($request->file('face_image'), 'driver/face');
                    if ($driver->faceImage) {
                        $this->deleteFile($driver->faceImage);
                    }
                }

                $driver->update($driverData);
            }

            // Reload user with updated data
            $user->refresh();
            $user->load(['country', 'state', 'city']);

            // Load driver relationship if user is a driver
            if ($isDriver) {
                $user->loadMissing('driver');
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => new UserResource($user)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
