<?php

namespace Modules\DriverCommission\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Modules\DriverCommission\app\Http\Requests\BulkUpdatePaymentStatusRequest;
use Modules\DriverCommission\app\Http\Requests\FilterDriverCommissionRequest;
use Modules\DriverCommission\app\Http\Requests\StoreDriverCommissionRequest;
use Modules\DriverCommission\app\Http\Requests\UpdateDriverCommissionRequest;
use Modules\DriverCommission\app\Http\Resources\DriverCommissionCollection;
use Modules\DriverCommission\app\Http\Resources\DriverCommissionResource;
use Modules\DriverCommission\app\Http\Resources\DriverEarningsSummaryCollection;
use Modules\DriverCommission\app\Http\Resources\DriverStatsResource;
use Modules\DriverCommission\app\Models\DriverCommission;
use Modules\DriverCommission\app\Repositories\DriverCommissionRepository;
use Modules\DriverCommission\app\Repositories\DriverCommissionRepositoryInterface;

class DriverCommissionController extends Controller
{
    public function __construct(
        private DriverCommissionRepository $commissionRepository
    ) {
        
        $this->commissionRepository = $commissionRepository;
    }

    /**
     * Display a paginated list of driver commissions
     */
    public function index(FilterDriverCommissionRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $filters['per_page'] ?? 15;

        if (isset($filters['driver_id'])) {
            $commissions = $this->commissionRepository->getDriverCommissions(
                $filters['driver_id'],
                $filters,
                $perPage
            );
        } else {
            $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date']) : null;
            $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date']) : null;
            
            $commissions = $this->commissionRepository->getCommissionsByDateRange(
                $startDate ?? now()->subDays(30),
                $endDate ?? now(),
                $filters,
                $perPage
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Driver commissions retrieved successfully',
            'data' => new DriverCommissionCollection($commissions),
        ]);
    }

    /**
     * Store a new driver commission
     */
    public function store(StoreDriverCommissionRequest $request): JsonResponse
    {
        $commission = $this->commissionRepository->createDriverCommission($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Driver commission created successfully',
            'data' => new DriverCommissionResource($commission->load(['driver', 'rideAssignment'])),
        ], 201);
    }

    /**
     * Display a specific driver commission
     */
    public function show(DriverCommission $commission): JsonResponse
    {
        $commission->load(['driver', 'rideAssignment']);

        return response()->json([
            'success' => true,
            'message' => 'Driver commission retrieved successfully',
            'data' => new DriverCommissionResource($commission),
        ]);
    }

    /**
     * Update a driver commission
     */
    public function update(UpdateDriverCommissionRequest $request, DriverCommission $commission): JsonResponse
    {
        $updatedCommission = $this->commissionRepository->update($commission->id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Driver commission updated successfully',
            'data' => new DriverCommissionResource($updatedCommission->load(['driver', 'rideAssignment'])),
        ]);
    }

    /**
     * Remove a driver commission
     */
    public function destroy(DriverCommission $commission): JsonResponse
    {
        $this->commissionRepository->delete($commission->id);

        return response()->json([
            'success' => true,
            'message' => 'Driver commission deleted successfully',
        ]);
    }

    /**
     * Get driver commissions by status
     */
    public function getByStatus(Request $request, string $status): JsonResponse
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = $request->get('per_page', 15);
        $commissions = $this->commissionRepository->getCommissionsByStatus($status, $perPage);

        return response()->json([
            'success' => true,
            'message' => "Commissions with status '{$status}' retrieved successfully",
            'data' => new DriverCommissionCollection($commissions),
        ]);
    }

    /**
     * Get driver earnings summary
     */
    public function getDriverSummary(Request $request, int $driverId): JsonResponse
    {
        $request->validate([
            'summary_type' => 'nullable|in:daily,weekly,monthly',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $summaryType = $request->get('summary_type', 'daily');
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;
        $perPage = $request->get('per_page', 15);

        $summary = $this->commissionRepository->getDriverEarningsSummary(
            $driverId,
            $summaryType,
            $startDate,
            $endDate,
            $perPage
        );

        return response()->json([
            'success' => true,
            'message' => 'Driver earnings summary retrieved successfully',
            'data' => new DriverEarningsSummaryCollection($summary),
        ]);
    }

    /**
     * Get driver statistics
     */
    public function getDriverStats(Request $request, int $driverId): JsonResponse
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        $stats = $this->commissionRepository->getDriverStats($driverId, $startDate, $endDate);
        $stats['driver_id'] = $driverId;
        $stats['start_date'] = $startDate?->toDateString();
        $stats['end_date'] = $endDate?->toDateString();

        return response()->json([
            'success' => true,
            'message' => 'Driver statistics retrieved successfully',
            'data' => new DriverStatsResource($stats),
        ]);
    }

    /**
     * Bulk update payment status
     */
    public function bulkUpdatePaymentStatus(BulkUpdatePaymentStatusRequest $request): JsonResponse
    {
        $data = $request->validated();
        $additionalData = [];

        if ($data['payment_status'] === 'paid') {
            $additionalData['payment_date'] = now();
            if (!empty($data['payment_method'])) {
                $additionalData['payment_method'] = $data['payment_method'];
            }
            if (!empty($data['payment_reference'])) {
                $additionalData['payment_reference'] = $data['payment_reference'];
            }
        }

        if ($data['payment_status'] === 'failed' && !empty($data['failure_reason'])) {
            $additionalData['metadata->failure_reason'] = $data['failure_reason'];
            $additionalData['metadata->failed_at'] = now()->toISOString();
        }

        $updatedCount = $this->commissionRepository->bulkUpdatePaymentStatus(
            $data['commission_ids'],
            $data['payment_status'],
            $additionalData
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully updated {$updatedCount} commission(s) to status '{$data['payment_status']}'",
            'data' => [
                'updated_count' => $updatedCount,
                'payment_status' => $data['payment_status'],
            ],
        ]);
    }

    /**
     * Get top earning drivers
     */
    public function getTopEarningDrivers(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;
        $limit = $request->get('limit', 10);

        $topDrivers = $this->commissionRepository->getTopEarningDrivers($startDate, $endDate, $limit);

        return response()->json([
            'success' => true,
            'message' => 'Top earning drivers retrieved successfully',
            'data' => $topDrivers->map(function ($driver) {
                return [
                    'driver' => [
                        'id' => $driver->driver->id,
                        'name' => $driver->driver->name,
                        'email' => $driver->driver->email,
                    ],
                    'total_earnings' => number_format($driver->total_earnings, 2),
                ];
            }),
        ]);
    }

    /**
     * Get earnings trend for a driver
     */
    public function getDriverEarningsTrend(Request $request, int $driverId): JsonResponse
    {
        $request->validate([
            'period' => 'nullable|in:daily,weekly,monthly',
            'days' => 'nullable|integer|min:1|max:365',
        ]);

        $period = $request->get('period', 'daily');
        $days = $request->get('days', 30);

        $trend = $this->commissionRepository->getEarningsTrend($driverId, $period, $days);

        return response()->json([
            'success' => true,
            'message' => 'Driver earnings trend retrieved successfully',
            'data' => [
                'driver_id' => $driverId,
                'period' => $period,
                'days' => $days,
                'trend' => $trend->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'earnings' => number_format($item->total_earnings, 2),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Mark commission as paid
     */
    public function markAsPaid(Request $request, DriverCommission $commission): JsonResponse
    {
        $request->validate([
            'payment_method' => 'required|string|max:255',
            'payment_reference' => 'nullable|string|max:255|unique:driver_commissions,payment_reference',
        ]);

        $success = $commission->markAsPaid(
            $request->payment_method,
            $request->payment_reference
        );

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark commission as paid',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Commission marked as paid successfully',
            'data' => new DriverCommissionResource($commission->fresh()->load(['driver', 'rideAssignment'])),
        ]);
    }

    /**
     * Mark commission as failed
     */
    public function markAsFailed(Request $request, DriverCommission $commission): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $success = $commission->markAsFailed($request->reason);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark commission as failed',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Commission marked as failed successfully',
            'data' => new DriverCommissionResource($commission->fresh()->load(['driver', 'rideAssignment'])),
        ]);
    }
}