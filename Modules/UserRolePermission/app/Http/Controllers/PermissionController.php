<?php

namespace Modules\UserRolePermission\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\UserRolePermission\App\Http\Requests\PermissionRequest;
use Spatie\Permission\Models\Permission;
use Exception;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    public function index()
    {
        return view('userrolepermission::permission');
    }

    public function store(PermissionRequest $request)
    {
        try {
            $permission = Permission::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Permission created successfully',
                'permission' => $permission
            ], 201);

        } catch (Exception $e) {
            Log::error('Role creation failed: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to create role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Permission $permission)
    {
        return response()->json($permission);
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        try {
            $permission->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Permission Update successfully',
                'permission' => $permission
            ], 201);

        } catch (Exception $e) {
            Log::error('Permission creation failed: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to update permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Soft delete a permission
    public function destroy(Permission $permission)
    {
        try {
            $permission->delete();  // Soft delete
            return response()->json(['message' => 'Permission deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete permission.', 'error' => $e->getMessage()], 500);
        }
    }

    // Restore a soft deleted permission
    public function restore($id)
    {
        try {
            $permission = Permission::withTrashed()->findOrFail($id);
            $permission->restore();
            return response()->json(['message' => 'Permission restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to restore permission.', 'error' => $e->getMessage()], 500);
        }
    }

    // Permanently delete a permission
    public function forceDelete($id)
    {
        try {
            $permission = Permission::withTrashed()->findOrFail($id);
            $permission->forceDelete();
            return response()->json(['message' => 'Permission permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to permanently delete permission.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        $data = ($request->has('trashed') && $request->trashed == 'true')
            ? Permission::onlyTrashed()->orderBy('id', 'DESC')->get()
            : Permission::orderBy('id', 'DESC')->get();

        $html = view('userrolepermission::components.role_row', compact('data'))->render();

        return response()->json(['html' => $html]);
    }

}
