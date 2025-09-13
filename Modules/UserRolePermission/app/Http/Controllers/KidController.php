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
use Illuminate\Support\Facades\Auth;
use Modules\UserRolePermission\app\Http\Resources\KidResource;
use Modules\UserRolePermission\app\Repositories\KidRepository;

class KidController extends Controller
{
    use Upload;

    protected $kidRepository;

    public function __construct(KidRepository $kidRepository)
    {
        $this->kidRepository = $kidRepository;
    }

    public function index(Request $request)
    {
        $parentId = $request->query('parent');
        return view('userrolepermission::kids.index', compact('parentId'));
    }

    public function store(KidRequest $request)
    {
        try {
            $data = $request->validated();

            $kid = $this->kidRepository->create($data);

            return response()->json([
                'message' => 'Kid created successfully',
                'kid' => new KidResource($kid)
            ], 200);

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
        return response()->json(new KidResource($kid));
    }

    public function update(KidRequest $request, Kid $kid)
    {
        try {
            $data = $request->validated();

            $kid = $this->kidRepository->update($kid->id, $data);

            return response()->json([
                'message' => 'Kid updated successfully',
                'kid' => new KidResource($kid)
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
        return response()->json(new KidResource($kid));
    }

    // Soft delete a kid
    public function destroy(Kid $kid)
    {
        try {
            $this->kidRepository->delete($kid->id);
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
            $this->kidRepository->forceDelete($id);
            return response()->json(['message' => 'Kid permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to permanently delete kid.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        $parentId = $request->query('parent');
        $data = $this->kidRepository->getData($request, $parentId);
        $html = view('userrolepermission::kids.kid_row', compact('data'))->render();

        return response()->json(['html' => $html]);
    }

    public function parents()
    {
        $parents = $this->kidRepository->getParents();

        return response()->json(['parents' => $parents]);
    }
}
