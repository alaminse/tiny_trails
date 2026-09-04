<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Models\VerificationCode;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Modules\LocationManagement\app\Models\City;
use Modules\LocationManagement\app\Models\Country;
use Modules\LocationManagement\app\Models\State;
use Modules\UserRolePermission\app\Http\Resources\UserResource;

class AuthController extends Controller
{
    use Upload;

    public function getCountries()
    {
        try {
            $countries = Country::select('id', 'name')
                ->where('status', 'active')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Countries retrieved successfully',
                'data' => $countries,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve countries',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function allStates()
    {
        try {
            $states = State::select('id', 'name', 'country_id')
                ->where('status', 'active')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'States retrieved successfully',
                'data' => $states,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve states',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function allCities()
    {
        try {
            $cities = City::select('id', 'name', 'state_id')
                ->where('status', 'active')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Cities retrieved successfully',
                'data' => $cities,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cities',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getStateByCity($cityId)
    {
        try {
            // City find
            $city = City::find($cityId);

            if (! $city) {
                return response()->json([
                    'success' => false,
                    'message' => 'City not found',
                ], 404);
            }

            // City from State
            $state = State::find($city->state_id);

            if (! $state) {
                return response()->json([
                    'success' => false,
                    'message' => 'State not found for this city',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'State retrieved successfully',
                'data' => $state,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve state',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getStates($country_id)
    {
        try {
            $states = State::select('id', 'name', 'country_id')
                ->where('country_id', $country_id)
                ->where('status', 'active')
                ->orderBy('name', 'asc')
                ->get();

            if ($states->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No states found for this country',
                    'data' => [],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'States retrieved successfully',
                'data' => $states,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve states',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCities($state_id)
    {
        try {
            $cities = City::select('id', 'name', 'state_id')
                ->where('state_id', $state_id)
                ->where('status', 'active')
                ->orderBy('name', 'asc')
                ->get();

            if ($cities->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No cities found for this state',
                    'data' => [],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cities retrieved successfully',
                'data' => $cities,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cities',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'required|string|max:20|unique:users,phone',
                'dob' => 'required|date|before:today',
                'gender' => 'required|in:male,female,other',
                'height_cm' => 'nullable|numeric|min:0|max:300',
                'weight_kg' => 'nullable|numeric|min:0|max:500',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'address' => 'required|string',
                'country_id' => 'required|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'status' => 'nullable|in:active,inactive,pending',
            ]);

            // ✅ যদি validation fail করে
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // ✅ validated data
            $validated = $validator->validated();

            DB::beginTransaction();

            if ($request->file('photo')) {
                $userData['photo'] = $this->uploadFile($request->file('photo'), 'parent/profile');
            }

            // Hash password
            $validated['password'] = Hash::make($validated['password']);

            // Set default status if not provided
            $validated['status'] = $validated['status'] ?? 'active';

            // Create user
            $user = User::create($validated);

            // Assign parent role
            $user->assignRole('parent');

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Parent registered successfully',
                'data' => [
                    'user' => $user->load('roles'),
                    'token' => $token,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is '.$user->status,
            ], 403);
        }

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
                'role' => $user->getRoleNames(),
                'token_type' => 'Bearer',
                'verification_status' => $user->verification_status,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'role' => $request->user()->getRoleNames(),
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
                'data' => new UserResource($user),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();

            // Get validated data
            $validated = $request->validated();

            // Update basic fields
            $basicFields = [
                'first_name', 'last_name', 'email', 'phone',
                'dob', 'gender', 'height_cm', 'weight_kg',
                'address', 'country_id', 'state_id', 'city_id',
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
                    'car_color', 'car_plate_number',
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
                'data' => $user,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: '.$e->getMessage(),
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password reset link sent to your email',
                ], 200);
            }

            // Log the actual status for debugging
            Log::error('Password reset failed', ['status' => $status]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset link: '.$status,
            ], 500);

        } catch (\Exception $e) {
            Log::error('Password reset error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to reset password',
        ], 400);
    }

    public function sendVerificationCodes(User $user)
    {
        $phoneCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $emailCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        VerificationCode::create([
            'verifiable_type' => User::class,
            'verifiable_id' => $user->id,
            'type' => 'phone',
            'code' => Hash::make($phoneCode),
            'expires_at' => now()->addMinutes(10),
        ]);

        VerificationCode::create([
            'verifiable_type' => User::class,
            'verifiable_id' => $user->id,
            'type' => 'email',
            'code' => Hash::make($emailCode),
            'expires_at' => now()->addMinutes(15),
        ]);

        // এখন SMS এবং ইমেল পাঠান
        // Log::info("Sending phone code {$phoneCode} to {$parent->phone}");
        // Mail::to($parent->email)->send(new YourEmailVerificationMail($emailCode));

        return [
            'phone_code' => $phoneCode,
            'email_code' => $emailCode,
        ];
    }

    public function verifyPhone(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'code' => 'required|string|digits:6',
        ]);

        $user = User::findOrFail($request->user_id);

        $verificationCode = VerificationCode::findLatestValid($user, 'phone');

        if (! $verificationCode || ! Hash::check($request->code, $verificationCode->code)) {
            return response()->json(['message' => 'Invalid or expired verification code.'], 422);
        }

        $verificationCode->delete();
        $user->phone_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Phone number verified successfully.']);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'code' => 'required|string|digits:6',
        ]);

        $user = User::findOrFail($request->user_id);

        $verificationCode = VerificationCode::findLatestValid($user, 'email');

        if (! $verificationCode || ! Hash::check($request->code, $verificationCode->code)) {
            return response()->json(['message' => 'Invalid or expired verification code.'], 422);
        }

        $verificationCode->delete();
        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Email verified successfully.']);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        DB::beginTransaction();
        try {
            $subscriptionIds = DB::table('subscriptions')
                ->where('user_id', $user->id)
                ->pluck('id');

            if ($subscriptionIds->isNotEmpty()) {
                DB::table('payway_transactions')
                    ->whereIn('subscription_id', $subscriptionIds)
                    ->delete();
            }


            $kidIds = DB::table('kids')->where('user_id', $user->id)->pluck('id');
            if ($kidIds->isNotEmpty()) {
                DB::table('kid_wages')->whereIn('kid_id', $kidIds)->delete();
                DB::table('kids')->where('user_id', $user->id)->delete();
            }


            DB::table('subscriptions')->where('user_id', $user->id)->delete();


            DB::table('personal_access_tokens')
                ->where('tokenable_id', $user->id)
                ->where('tokenable_type', get_class($user))
                ->delete();

            
            $user->forceDelete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Account deletion failed:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account: '.$e->getMessage(),
            ], 500);
        }
    }
}
