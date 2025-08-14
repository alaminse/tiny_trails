<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\UserRolePermission\app\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;

            $driverData = null;
            if ($user->driver) {
                $driver = $user->driver; // make sure $driver is defined
                $driverData = [
                    'name'              => $user->first_name,
                    'face_embedding'    => $driver->face_embedding,
                    'faceImage'         => getImageUrl($driver->faceImage),
                    'is_verified'       => $driver->is_verified,
                ];
            }

            return response()->json([
                'token'  => $token,
                'user'   => [
                    'id'         => $user->id,
                    'first_name' => $user->first_name,
                    'last_name'  => $user->last_name,
                    'email'      => $user->email,
                    'role'       => $user->getRoleNames(),
                    'driver'     => $driverData // null if no driver
                ]
            ], 200);

        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user) {
            $userData = new UserResource($user);
            return response()->json([
                'user' => $userData
            ], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
