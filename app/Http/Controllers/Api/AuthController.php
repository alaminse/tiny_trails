<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            $user = Auth::user()->load('roles');

            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user'  => [
                    'id'    => $user->id,
                    'first_name'  => $user->first_name,
                    'last_name'  => $user->last_name,
                    'email' => $user->email,
                    'role'  => $user->getRoleNames() // returns ['admin', 'editor']
                ]
            ], 200);

        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function profile()
    {
        /** @var \app\Models\User $user */
        $user = Auth::user();

        if($user) {
            $userData = $user->toArray();

            // Check if user role has parent then load parent data
            $roles = $user->getRoleNames();
            if ($roles && is_array($roles) && count($roles) > 0)
            {
                $role = $roles[0]; // Assuming user has only one role
                $roleModel = \Spatie\Permission\Models\Role::findByName($role);
                if ($roleModel && $roleModel->parent_id) {
                    $parentRole = \Spatie\Permission\Models\Role::find($roleModel->parent_id);
                    $userData['parent_role'] = $parentRole ? $parentRole->name : null;
                }
            }

            // If role is driver then load driver data
            if ($user->driver) {
                $userData['driver'] = $user->driver->toArray();
            }

            // Make a resource for image path and return
            $userData['image_path'] = asset('uploads/images/' . $user->image); // Assuming image is stored in uploads/images

            return response()->json([
                'user'  => $userData
            ], 200);

        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }
}
