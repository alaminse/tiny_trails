<?php

namespace Modules\UserRolePermission\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('kids');

        // Return collection of kids
        return response()->json(
            KidResource::collection($user->kids),
            200
        );
    }

    public function store(KidRequest $request)
    {

        try {
            $data = $request->validated();

            $kid = $this->kidRepository->create($data);

            return response()->json(new KidResource($kid), 201);

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
        $kid->load('parent');
        return response()->json(new KidResource($kid));
    }

    public function update(KidRequest $request, Kid $kid)
    {
        try {
            $data = $request->validated();

            $kid = $this->kidRepository->update($kid->id, $data);

            return response()->json(new KidResource($kid), 200);

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
}
