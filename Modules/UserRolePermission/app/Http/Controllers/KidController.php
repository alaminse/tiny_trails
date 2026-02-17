<?php

namespace Modules\UserRolePermission\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\Upload;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\app\Models\Plan;
use Modules\UserRolePermission\app\Http\Requests\KidRequest;
use Modules\UserRolePermission\app\Http\Resources\KidResource;
use Modules\UserRolePermission\app\Models\Kid;
use Modules\UserRolePermission\app\Models\KidWage;
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

    public function wage(Kid $kid)
    {
        $plans = Plan::where('status', 1)->get();
        $kid->load('parent');
        $data = new KidResource($kid);
        return view('userrolepermission::kids.wage', compact('data', 'plans'));
    }

    public function storeWage(Request $request, Kid $kid)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,inactive,pending',
            'notes' => 'nullable|string|max:1000'
        ], [
            'plan_id.required' => 'Please select a plan',
            'plan_id.exists' => 'The selected plan is invalid',
            'price.required' => 'Regular price is required',
            'price.numeric' => 'Regular price must be a number',
            'price.min' => 'Regular price must be 0 or greater',
            'sell_price.required' => 'Selling price is required',
            'sell_price.numeric' => 'Selling price must be a number',
            'sell_price.min' => 'Selling price must be 0 or greater',
            'start_date.required' => 'Start date is required',
            'start_date.after_or_equal' => 'Start date must be today or later',
            'end_date.after' => 'End date must be after start date',
            'status.required' => 'Please select a status'
        ]);

        try {
            $wage = new KidWage();
            $wage->kid_id = $kid->id;
            $wage->plan_id = $request->plan_id;
            $wage->price = $request->price;
            $wage->sell_price = $request->sell_price;
            $wage->start_date = $request->start_date;
            $wage->end_date = $request->end_date;
            $wage->status = $request->status;
            $wage->notes = $request->notes;
            $wage->save();

            return redirect()->route('admin.kids.index')
                            ->with('success', 'Wage plan assigned successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign wage plan: ' . $e->getMessage())
                        ->withInput();
        }
    }
}
