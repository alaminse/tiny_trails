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
use Modules\UserRolePermission\app\Http\Requests\FaceRequest;
use Modules\UserRolePermission\app\Http\Resources\KidResource;
use Modules\UserRolePermission\app\Repositories\KidRepository;

class FaceController extends Controller
{
    use Upload;

    public function store(FaceRequest $request)
    {
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get the related driver
        $driver = $user->driver;

        if ($driver) {
            if (isset($data['faceImage']) && $data['faceImage']) {
                $data['faceImage'] = $this->uploadFile($data['faceImage'], 'driver/'.$user->id);
            }

            // Update the driver record
            $driver->update([
                'face_embedding' => json_encode($data['embedding']),
                'faceImage'      => $data['faceImage'],
            ]);

            return response()->json([
                'message' => 'Face verified successfully',
                'driver' => $driver->fresh() // refresh to get latest data
            ], 200);
        }

        return response()->json(['message' => 'Driver not found for this user'], 404);
    }

    public function verification(FaceRequest $request)
    {
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get the related driver
        $driver = $user->driver;

        if ($driver) {
            if (isset($data['faceImage']) && $data['faceImage']) {
                $data['faceImage'] = $this->uploadFile($data['faceImage'], 'driver/'.$user->id);
            }

            // Update the driver record
            $driver->update([
                'face_embedding' => json_encode($data['embedding']),
                'faceImage'      => $data['faceImage'],
            ]);

            return response()->json([
                'message' => 'Face verified successfully',
                'driver' => $driver->fresh() // refresh to get latest data
            ], 200);
        }

        return response()->json(['message' => 'Driver not found for this user'], 404);
    }

    public function edit(Kid $kid)
    {
        $kid->load('parent');
        return response()->json(new KidResource($kid));
    }
}
