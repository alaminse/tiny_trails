<?php

namespace Modules\LocationManagement\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\LocationManagement\app\Http\Requests\CountryRequest;
use Modules\LocationManagement\app\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        return view('locationmanagement::country');
    }

    public function store(CountryRequest $request)
    {
        try {
            $country = Country::create([
                'name'                          => $request->name,
                'status'                        => $request->status,
            ]);

            return response()->json([
                'message' => 'Pickup Type created and role assigned successfully',
                'user' => $country
            ], 201);

        } catch (Exception $e) {
            Log::error('Pickup creation failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Country $country)
    {
        return response()->json($country);
    }

    public function update(CountryRequest $request, Country $country)
    {
        try {
            $country->update([
                'name'      => $request->name,
                'status'    => $request->status,
            ]);

            return response()->json([
                'message' => 'Country updated and role assigned successfully',
                'country' => $country,
            ], 200);

        } catch (Exception $e) {
            Log::error('Country update failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update Country',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    // Soft delete a Country
    public function destroy(Country $country)
    {
        try {
            $country->delete();  // Soft delete
            return response()->json(['message' => 'Country Type deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete Country.', 'error' => $e->getMessage()], 500);
        }
    }

    // Restore a soft deleted permission
    public function restore($id)
    {
        try {
            $country = Country::withTrashed()->findOrFail($id);
            $country->restore();
            return response()->json(['message' => 'Country restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to restore Country.', 'error' => $e->getMessage()], 500);
        }
    }

    // Permanently delete a user
    public function forceDelete($id)
    {
        try {
            $pickup_type = Country::withTrashed()->findOrFail($id);
            $pickup_type->forceDelete();
            return response()->json(['message' => 'Country permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to permanently delete Country.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        $data = ($request->has('trashed') && $request->trashed == 'true')
            ? Country::onlyTrashed()->orderBy('id', 'DESC')->get()
            : Country::orderBy('id', 'DESC')->get();
        $html = view('locationmanagement::components.country_row', compact('data'))->render();

        return response()->json(['html' => $html]);
    }
}
