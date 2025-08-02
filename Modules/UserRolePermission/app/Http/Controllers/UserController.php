<?php

namespace Modules\UserRolePermission\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\UserRolePermission\App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function index()
    {
        return view('userrolepermission::index');
    }

    public function store(UserRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $user->assignRole($request->role);

            DB::commit();

            return response()->json([
                'message' => 'User created and role assigned successfully',
                'user' => $user
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('User creation failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(User $user)
    {
        $roles = $user->getRoleNames();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $roles->first(),
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            // Update user fields
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update password only if provided
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
                $user->save();
            }

            $user->syncRoles($request->role);
            DB::commit();

            return response()->json([
                'message' => 'User updated and role assigned successfully',
                'user' => $user,
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('User update failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    // Soft delete a user
    public function destroy(User $user)
    {
        try {
            $user->delete();  // Soft delete
            return response()->json(['message' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user.', 'error' => $e->getMessage()], 500);
        }
    }

    // Restore a soft deleted permission
    public function restore($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();
            return response()->json(['message' => 'User restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to restore user.', 'error' => $e->getMessage()], 500);
        }
    }

    // Permanently delete a user
    public function forceDelete($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->forceDelete();
            return response()->json(['message' => 'User permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to permanently delete user.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        $data = ($request->has('trashed') && $request->trashed == 'true')
            ? User::onlyTrashed()->orderBy('id', 'DESC')->get()
            : User::orderBy('id', 'DESC')->get();

        $html = view('userrolepermission::components.user_row', compact('data'))->render();

        return response()->json(['html' => $html]);
    }

}
