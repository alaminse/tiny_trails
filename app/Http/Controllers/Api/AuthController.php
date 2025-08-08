<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            /** @var \App\Models\User $user */
            $user = Auth::user()->load('roles');

            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user'  => $user->toArray() + [
                    'role' => $user->getRoleNames()
                ]
            ], 200);

        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }
}
