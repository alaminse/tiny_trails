<?php

namespace Modules\RideAssignment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\RideAssignment\app\Http\Requests\StoreRideAssignmentRequest;
use Modules\RideAssignment\app\Http\Requests\UpdateRideAssignmentRequest;
use Modules\RideAssignment\app\Http\Resources\RideAssignmentResource;
use Modules\RideAssignment\app\Http\Resources\RideAssignmentCollection;
use Modules\RideAssignment\app\Repositories\RideAssignmentRepository;
use App\Models\User;
use Modules\UserRolePermission\app\Models\Kid;
use Modules\Subscription\app\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RideAssignmentController extends Controller
{
    protected $rideRepository;

    public function __construct(RideAssignmentRepository $rideRepository)
    {
        $this->rideRepository = $rideRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $stats = $this->rideRepository->getSystemStats();
        
        // Get drivers and parents using Spatie roles with active status
        $drivers = User::role('driver')
            ->where('status', 'active')
            ->with('driver') // Load driver relationship
            ->get();
            
        $parents = User::role('parent')
            ->where('status', 'active')
            ->get();
        
        // Alternative using your scope methods
        // $drivers = User::drivers()->where('is_active', true)->with('driver')->get();
        // $parents = User::parents()->where('is_active', true)->get();
        
        $kids = Kid::with('parent')->get();
        $subscriptions = Subscription::active()->with(['user', 'plan'])->get();
        
        return view('rideassignment::index', compact('stats', 'drivers', 'parents', 'kids', 'subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Get drivers and parents using Spatie roles
        $drivers = User::role('driver')
            ->where('is_active', true)
            ->with('driver')
            ->get();
            
        $parents = User::role('parent')
            ->where('is_active', true)
            ->get();
        
        $kids = Kid::with('user')->get();
        $subscriptions = Subscription::active()->with(['user', 'plan'])->get();
        
        return view('ridemanagement::ride-assignment.create', compact('drivers', 'parents', 'kids', 'subscriptions'));
    }

    /**
     * Get available drivers for a ride (updated for Spatie)
     */
    public function getAvailableDrivers(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ride_date' => 'required|date',
                'pickup_time' => 'required|date_format:H:i'
            ]);

            $drivers = $this->rideRepository->getAvailableDrivers(
                $request->ride_date,
                $request->pickup_time
            );

            return response()->json([
                'success' => true,
                'data' => $drivers->map(function ($driver) {
                    return [
                        'id' => $driver->id,
                        'name' => $driver->name,
                        'email' => $driver->email,
                        'phone' => $driver->phone ?? $driver->driver->phone ?? 'N/A', // Check both user and driver relationship
                        'driver_info' => $driver->driver ? [
                            'license_number' => $driver->driver->license_number ?? null,
                            'vehicle_info' => $driver->driver->vehicle_info ?? null,
                            'rating' => $driver->driver->rating ?? null,
                        ] : null
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch available drivers.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRideAssignmentRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            $ride = $this->rideRepository->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Ride assignment created successfully.',
                'data' => new RideAssignmentResource($ride->load(['driver', 'parent', 'kid']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ride assignment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $ride = $this->rideRepository->findById($id, ['driver', 'parent', 'kid', 'subscription.plan', 'cancelledBy']);
            
            if (!$ride) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ride assignment not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new RideAssignmentResource($ride)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ride assignment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): JsonResponse
    {
        try {
            $ride = $this->rideRepository->findById($id, ['driver', 'parent', 'kid']);
            
            if (!$ride) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ride assignment not found.'
                ], 404);
            }

            return response()->json(new RideAssignmentResource($ride));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ride assignment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRideAssignmentRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            
            $updated = $this->rideRepository->update($id, $data);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ride assignment not found.'
                ], 404);
            }

            $ride = $this->rideRepository->findById($id, ['driver', 'parent', 'kid']);

            return response()->json([
                'success' => true,
                'message' => 'Ride assignment updated successfully.',
                'data' => new RideAssignmentResource($ride)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ride assignment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->rideRepository->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ride assignment not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ride assignment moved to trash successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ride assignment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $restored = $this->rideRepository->restore($id);

            if (!$restored) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ride assignment not found in trash.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ride assignment restored successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore ride assignment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete(int $id): JsonResponse
    {
        try {
            $deleted = $this->rideRepository->forceDelete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ride assignment not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ride assignment permanently deleted.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete ride assignment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get data for DataTable
     */
    public function getData(Request $request): JsonResponse
    {
        try {
            $trashed = $request->boolean('trashed', false);
            $data = $this->rideRepository->getDataTableData($trashed);

            $html = view('rideassignment::table-rows', compact('data'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'data' => new RideAssignmentCollection($data)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ride assignments.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accept ride assignment
     */
    public function accept(int $id): JsonResponse
    {
        try {
            $accepted = $this->rideRepository->acceptRide($id);

            if (!$accepted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot accept this ride assignment.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ride assignment accepted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept ride assignment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start ride assignment
     */
    public function start(int $id): JsonResponse
    {
        try {
            $started = $this->rideRepository->startRide($id);

            if (!$started) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot start this ride assignment.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ride assignment started successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start ride assignment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete ride assignment
     */
    public function complete(int $id): JsonResponse
    {
        try {
            $completed = $this->rideRepository->completeRide($id);

            if (!$completed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot complete this ride assignment.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ride assignment completed successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete ride assignment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel ride assignment
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'reason' => 'nullable|string|max:500'
            ]);

            $cancelled = $this->rideRepository->cancelRide(
                $id, 
                $request->reason, 
                Auth::id()
            );

            if (!$cancelled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel this ride assignment.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ride assignment cancelled successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel ride assignment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark ride as no show
     */
    public function markAsNoShow(int $id): JsonResponse
    {
        try {
            $marked = $this->rideRepository->markAsNoShow($id);

            if (!$marked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ride assignment not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ride assignment marked as no show.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark as no show.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk assign rides to driver
     */
    public function bulkAssign(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ride_ids' => 'required|array',
                'ride_ids.*' => 'exists:ride_assignments,id',
                'driver_id' => 'required|exists:users,id'
            ]);

            $assigned = $this->rideRepository->bulkAssign(
                $request->ride_ids,
                $request->driver_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Rides assigned successfully.',
                'assigned_count' => $assigned
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign rides.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk cancel rides
     */
    public function bulkCancel(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ride_ids' => 'required|array',
                'ride_ids.*' => 'exists:ride_assignments,id',
                'reason' => 'nullable|string|max:500'
            ]);

            $cancelled = $this->rideRepository->bulkCancel(
                $request->ride_ids,
                $request->reason,
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Rides cancelled successfully.',
                'cancelled_count' => $cancelled
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel rides.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver rides
     */
    public function getDriverRides(int $driverId): JsonResponse
    {
        try {
            $rides = $this->rideRepository->getByDriver($driverId, ['parent', 'kid']);

            return response()->json([
                'success' => true,
                'data' => new RideAssignmentCollection($rides)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch driver rides.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get parent rides
     */
    public function getParentRides(int $parentId): JsonResponse
    {
        try {
            $rides = $this->rideRepository->getByParent($parentId, ['driver', 'kid']);

            return response()->json([
                'success' => true,
                'data' => new RideAssignmentCollection($rides)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch parent rides.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's rides
     */
    public function getTodaysRides(): JsonResponse
    {
        try {
            $rides = $this->rideRepository->getTodaysRides(['driver', 'parent', 'kid']);

            return response()->json([
                'success' => true,
                'data' => new RideAssignmentCollection($rides)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch today\'s rides.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search rides
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $term = $request->get('term', '');
            $rides = $this->rideRepository->search($term, ['driver', 'parent', 'kid']);

            return response()->json([
                'success' => true,
                'data' => new RideAssignmentCollection($rides)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search rides.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = $this->rideRepository->getSystemStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}