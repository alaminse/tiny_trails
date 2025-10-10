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

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = auth()->user();
            
            // Get validated data
            $validated = $request->validated();
            
            // Update basic fields
            $basicFields = [
                'first_name', 'last_name', 'email', 'phone', 
                'dob', 'gender', 'height_cm', 'weight_kg',
                'address', 'country_id', 'state_id', 'city_id'
            ];
            
            foreach ($basicFields as $field) {
                if (isset($validated[$field])) {
                    $user->$field = $validated[$field];
                }
            }

            // Update password if provided
            if (isset($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            // Update driver-specific fields (only if user is a driver)
            if ($user->hasRole('driver')) {
                $driverFields = [
                    'driving_license_number', 'driving_license_expiry',
                    'car_model', 'car_make', 'car_year', 
                    'car_color', 'car_plate_number'
                ];
                
                foreach ($driverFields as $field) {
                    if (isset($validated[$field])) {
                        $user->$field = $validated[$field];
                    }
                }
            }

            if ($request->file('photo')) {
                $userData['photo'] = $this->uploadFile($request->file('photo'), 'user');
                if ($user->photo) {
                    $this->deleteFile($user->photo);
                }
            }

            // Handle driver images (only if user is a driver)
            if ($user->hasRole('driver')) {
                if ($request->file('driving_license_image')) {
                    $userData['driving_license_image'] = $this->uploadFile($request->file('driving_license_image'), 'driver');
                    if ($user->driving_license_image) {
                        $this->deleteFile($user->driving_license_image);
                    }
                }

                if ($request->file('car_image')) {
                    $userData['car_image'] = $this->uploadFile($request->file('car_image'), 'driver');
                    if ($user->car_image) {
                        $this->deleteFile($user->car_image);
                    }
                }
                
                if ($request->file('face_image')) {
                    $userData['face_image'] = $this->uploadFile($request->file('face_image'), 'driver');
                    if ($user->face_image) {
                        $this->deleteFile($user->face_image);
                    }
                }
            }

            $user->save();

            // Reload user with relationships if needed
            $user->load(['country', 'state', 'city', 'roles']);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }
}
