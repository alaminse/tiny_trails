<?php

namespace Modules\UserRolePermission\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\UserRolePermission\app\Http\Requests\KidRequest;
use Modules\UserRolePermission\app\Models\Kid;
use App\Traits\Upload;

class KidController extends Controller
{
    use Upload;
    public function index(Request $request)
    {
        $parentId = $request->query('parent');
        return view('userrolepermission::kids.index', compact('parentId'));
    }

    public function store(KidRequest $request)
    {
        try {
            $data = $request->validated();

            if($request->file('photo'))
            {
                $data['photo'] = $this->uploadFile($request->file('photo'), 'kid');
            }

            $kid = Kid::create([
                'first_name'        => $data['first_name'],
                'last_name'         => $data['last_name'],
                'dob'               => $data['dob'] ?? null,
                'gender'            => $data['gender'] ?? null,
                'height_cm'         => $data['height_cm'] ?? null,
                'weight_kg'         => $data['weight_kg'] ?? null,
                'school_name'       => $data['school_name'],
                'school_address'    => $data['school_address'],
                'emergency_contact' => $data['emergency_contact'],
                'user_id'           => $data['user_id'] ?? null,
                'photo'             => $data['photo'],
            ]);

            return response()->json([
                'message' => 'Kid created successfully',
                'kid' => $kid
            ], 201);

        } catch (Exception $e) {
            Log::error('Kid creation failed: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to create kid',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Kid $kid)
    {
        $data = [
            'id'                => $kid->id,
            'user_id'           => $kid->user_id,
            'first_name'        => $kid->first_name,
            'last_name'         => $kid->last_name,
            'dob'               => $kid->dob,
            'gender'            => $kid->gender,
            'height_cm'         => $kid->height_cm,
            'weight_kg'         => $kid->weight_kg,
            'photo'             => $kid->photo ? asset($kid->photo) : null,
            'school_name'       => $kid->school_name,
            'school_address'    => $kid->school_address,
            'emergency_contact' => $kid->emergency_contact,
            'created_at'        => $kid->created_at,
            'updated_at'        => $kid->updated_at,
            'deleted_at'        => $kid->deleted_at,
        ];

        return response()->json($data);
    }

    public function update(KidRequest $request, Kid $kid)
    {
        try {
            $data = $request->validated();

            if ($request->file('photo')) {
                // Upload new photo and set it
                $data['photo'] = $this->uploadFile($request->file('photo'), 'kid');
                if($kid->photo) $this->deleteFile($kid->photo);
            }

            $kid->update([
                'first_name'        => $data['first_name'],
                'last_name'         => $data['last_name'],
                'dob'               => $data['dob'] ?? null,
                'gender'            => $data['gender'] ?? null,
                'height_cm'         => $data['height_cm'] ?? null,
                'weight_kg'         => $data['weight_kg'] ?? null,
                'school_name'       => $data['school_name'],
                'school_address'    => $data['school_address'],
                'emergency_contact' => $data['emergency_contact'],
                'user_id'           => $data['user_id'] ?? null,
                'photo'             => $data['photo'],
            ]);

            return response()->json([
                'message' => 'Kid updated successfully',
                'kid' => $kid
            ], 200);

        } catch (Exception $e) {
            Log::error('Kid update failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update kid',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Kid $kid)
    {
        // Eager load parent to avoid extra queries
        $kid->load('parent');

        $data = [
            'id'                => $kid->id,
            'user_id'           => $kid->user_id,
            'first_name'        => $kid->first_name,
            'last_name'         => $kid->last_name,
            'dob'               => $kid->dob,
            'gender'            => $kid->gender,
            'height_cm'         => $kid->height_cm,
            'weight_kg'         => $kid->weight_kg,
            'photo'             => $kid->photo ? asset($kid->photo) : null,
            'school_name'       => $kid->school_name,
            'school_address'    => $kid->school_address,
            'emergency_contact' => $kid->emergency_contact,
            'created_at'        => $kid->created_at,
            'updated_at'        => $kid->updated_at,
            'deleted_at'        => $kid->deleted_at,

            // Parent info
            'parent_first_name' => $kid->parent->first_name,
            'parent_last_name'  => $kid->parent->last_name,
            'parent_email'      => $kid->parent->email,
            'parent_phone'      => $kid->parent->phone,
            'parent_dob'        => $kid->parent->dob,
            'parent_gender'     => $kid->parent->gender,
            'parent_photo'      => $kid->parent->photo ? asset($kid->parent->photo) : null,
        ];

        return response()->json($data);
    }

    // Soft delete a kid
    public function destroy(Kid $kid)
    {
        try {
            $kid->delete();  // Soft delete
            return response()->json(['message' => 'Kid deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete kid.', 'error' => $e->getMessage()], 500);
        }
    }

    // Restore a soft deleted role
    public function restore($id)
    {
        try {
            $kid = Kid::withTrashed()->findOrFail($id);
            $kid->restore();
            return response()->json(['message' => 'Kid restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to restore kid.', 'error' => $e->getMessage()], 500);
        }
    }

    // Permanently delete a kid
    public function forceDelete($id)
    {
        try {
            $kid = Kid::withTrashed()->findOrFail($id);
            $kid->forceDelete();
            return response()->json(['message' => 'Kid permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to permanently delete kid.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        $query = Kid::orderBy('id', 'DESC');

        // Check if trashed is requested
        if ($request->filled('trashed') && $request->trashed == 'true') {
            $query = $query->onlyTrashed();
        }

        if (!empty($request->parent) && $request->parent !== 'null') {
            $parentId = $request->parent;
            $query = $query->where('user_id', $parentId);
        }

        $data = $query->get();
        $html = view('userrolepermission::kids.kid_row', compact('data'))->render();

        return response()->json(['html' => $html]);
    }

    public function parents()
    {
        $parents = User::whereHas('roles', function ($query) {
                $query->where('name', 'parent');
            })
            ->where('status', 1)
            ->orderByDesc('id')
            ->get(['id', 'first_name', 'last_name']);

        return response()->json(['parents' => $parents]);
    }
}
