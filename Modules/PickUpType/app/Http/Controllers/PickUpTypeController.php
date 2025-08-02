<?php

namespace Modules\PickUpType\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\PickUpType\App\Http\Requests\PickupRequest;
use Modules\PickUpType\App\Models\PickupType;

class PickUpTypeController extends Controller
{
    public function index()
    {
        return view('pickuptype::index');
    }

    public function store(PickupRequest $request)
    {
        try {
            $pickup_type = PickupType::create([
                'name'                          => $request->name,
                'amount'                        => $request->amount,
                'min_notice_minutes'            => $request->min_notice_minutes,
                'requires_instant_notification' => $request->requires_instant_notification,
                'status'                        => $request->status,
            ]);

            return response()->json([
                'message' => 'Pickup Type created and role assigned successfully',
                'user' => $pickup_type
            ], 201);

        } catch (Exception $e) {
            Log::error('Pickup creation failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(PickupType $pickuptype)
    {
        return response()->json($pickuptype);
    }

    public function update(PickupRequest $request, PickupType $pickuptype)
    {
        try {
            $pickuptype->update([
                'name'                          => $request->name,
                'amount'                        => $request->amount,
                'min_notice_minutes'            => $request->min_notice_minutes,
                'requires_instant_notification' => $request->requires_instant_notification,
                'status'                        => $request->status,
            ]);

            return response()->json([
                'message' => 'Pickup Type updated and role assigned successfully',
                'pickuptype' => $pickuptype,
            ], 200);

        } catch (Exception $e) {
            Log::error('Pickup Type update failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update Pickup Type',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    // Soft delete a Pickup Type
    public function destroy(PickupType $pickuptype)
    {
        try {
            $pickuptype->delete();  // Soft delete
            return response()->json(['message' => 'Pickup Type deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete Pickup Type.', 'error' => $e->getMessage()], 500);
        }
    }

    // Restore a soft deleted permission
    public function restore($id)
    {
        try {
            $pickuptype = PickupType::withTrashed()->findOrFail($id);
            $pickuptype->restore();
            return response()->json(['message' => 'Pickup Type restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to restore pickup type.', 'error' => $e->getMessage()], 500);
        }
    }

    // Permanently delete a user
    public function forceDelete($id)
    {
        try {
            $pickup_type = PickupType::withTrashed()->findOrFail($id);
            $pickup_type->forceDelete();
            return response()->json(['message' => 'PickupType permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to permanently delete Pickup Type.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        $data = ($request->has('trashed') && $request->trashed == 'true')
            ? PickupType::onlyTrashed()->orderBy('id', 'DESC')->get()
            : PickupType::orderBy('id', 'DESC')->get();
        $html = view('pickuptype::components.pickup_row', compact('data'))->render();

        return response()->json(['html' => $html]);
    }
}
