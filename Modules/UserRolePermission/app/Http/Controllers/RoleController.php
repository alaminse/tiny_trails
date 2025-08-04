<?php

namespace Modules\UserRolePermission\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\UserRolePermission\app\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    public function index()
    {
        return view('userrolepermission::role');
    }

    public function store(RoleRequest $request)
    {
        try {
            $role = Role::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Role created successfully',
                'role' => $role
            ], 201);

        } catch (Exception $e) {
            Log::error('Role creation failed: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to create role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Role $role)
    {
        return response()->json($role);
    }

    public function update(RoleRequest $request, Role $role)
    {
        try {
            $role->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Role Update successfully',
                'role' => $role
            ], 201);

        } catch (Exception $e) {
            Log::error('Role creation failed: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to update role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Soft delete a role
    public function destroy(Role $role)
    {
        try {
            $role->delete();  // Soft delete
            return response()->json(['message' => 'Role deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete role.', 'error' => $e->getMessage()], 500);
        }
    }

    // Restore a soft deleted role
    public function restore($id)
    {
        try {
            $role = Role::withTrashed()->findOrFail($id);
            $role->restore();
            return response()->json(['message' => 'Role restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to restore role.', 'error' => $e->getMessage()], 500);
        }
    }

    // Permanently delete a role
    public function forceDelete($id)
    {
        try {
            $role = Role::withTrashed()->findOrFail($id);
            $role->forceDelete();
            return response()->json(['message' => 'Role permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to permanently delete role.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        $data = ($request->has('trashed') && $request->trashed == 'true')
            ? Role::onlyTrashed()->orderBy('id', 'DESC')->get()
            : Role::orderBy('id', 'DESC')->get();

        $html = view('userrolepermission::components.role_row', compact('data'))->render();

        return response()->json(['html' => $html]);
    }

}
