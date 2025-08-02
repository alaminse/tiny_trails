<?php

namespace Modules\LocationManagement\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\LocationManagement\App\Http\Requests\CityRequest;
use Modules\LocationManagement\App\Http\Requests\StateRequest;
use Modules\LocationManagement\App\Models\City;
use Modules\LocationManagement\App\Models\State;

class CityController extends Controller
{
    public function index()
    {
        $states = State::where('status', 'active')->get();
        return view('locationmanagement::city', compact('states'));
    }

    public function store(CityRequest $request)
    {
        try {
            $city = City::create([
                'state_id'    => $request->state_id,
                'name'        => $request->name,
                'status'      => $request->status,
            ]);

            return response()->json([
                'message' => 'City created successfully',
                'city' => $city
            ], 201);

        } catch (Exception $e) {
            Log::error('City creation failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to city',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(City $city)
    {
        return response()->json($city);
    }

    public function update(CityRequest $request, City $city)
    {
        try {
            $city->update([
                'state_id'    => $request->state_id,
                'name'        => $request->name,
                'status'      => $request->status,
            ]);

            return response()->json([
                'message' => 'City updated successfully',
                'city' => $city,
            ], 200);

        } catch (Exception $e) {
            Log::error('City update failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update City',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    // Soft delete a city
    public function destroy(City $city)
    {
        try {
            $city->delete();  // Soft delete
            return response()->json(['message' => 'City Type deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete city.', 'error' => $e->getMessage()], 500);
        }
    }

    // Restore a soft deleted city
    public function restore($id)
    {
        try {
            $city = City::withTrashed()->findOrFail($id);
            $city->restore();
            return response()->json(['message' => 'City restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to restore City.', 'error' => $e->getMessage()], 500);
        }
    }

    // Permanently delete a city
    public function forceDelete($id)
    {
        try {
            $city = City::withTrashed()->findOrFail($id);
            $city->forceDelete();
            return response()->json(['message' => 'City permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to permanently delete City.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        $data = ($request->has('trashed') && $request->trashed == 'true')
            ? City::onlyTrashed()->orderBy('id', 'DESC')->get()
            : City::orderBy('id', 'DESC')->get();
        $html = view('locationmanagement::components.city_row', compact('data'))->render();

        return response()->json(['html' => $html]);
    }
}
