<?php

namespace Modules\LocationManagement\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\LocationManagement\app\Http\Requests\StateRequest;
use Modules\LocationManagement\app\Models\Country;
use Modules\LocationManagement\app\Models\State;

class StateController extends Controller
{
    public function index()
    {
        $countries = Country::where('status', 'active')->get();
        return view('locationmanagement::state', compact('countries'));
    }

    public function store(StateRequest $request)
    {
        try {
            $state = State::create([
                'country_id'    => $request->country_id,
                'name'          => $request->name,
                'status'        => $request->status,
            ]);

            return response()->json([
                'message' => 'State created successfully',
                'state' => $state
            ], 201);

        } catch (Exception $e) {
            Log::error('State creation failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to state',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(State $state)
    {
        return response()->json($state);
    }

    public function update(StateRequest $request, State $state)
    {
        try {
            $state->update([
                'country_id'    => $request->country_id,
                'name'          => $request->name,
                'status'        => $request->status,
            ]);

            return response()->json([
                'message' => 'State updated successfully',
                'state' => $state,
            ], 200);

        } catch (Exception $e) {
            Log::error('State update failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update State',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    // Soft delete a state
    public function destroy(State $state)
    {
        try {
            $state->delete();  // Soft delete
            return response()->json(['message' => 'State Type deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete state.', 'error' => $e->getMessage()], 500);
        }
    }

    // Restore a soft deleted permission
    public function restore($id)
    {
        try {
            $state = State::withTrashed()->findOrFail($id);
            $state->restore();
            return response()->json(['message' => 'State restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to restore State.', 'error' => $e->getMessage()], 500);
        }
    }

    // Permanently delete a State
    public function forceDelete($id)
    {
        try {
            $state = State::withTrashed()->findOrFail($id);
            $state->forceDelete();
            return response()->json(['message' => 'State permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to permanently delete State.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getData(Request $request)
    {
        $data = ($request->has('trashed') && $request->trashed == 'true')
            ? State::onlyTrashed()->orderBy('id', 'DESC')->get()
            : State::orderBy('id', 'DESC')->get();
        $html = view('locationmanagement::components.state_row', compact('data'))->render();

        return response()->json(['html' => $html]);
    }
}
