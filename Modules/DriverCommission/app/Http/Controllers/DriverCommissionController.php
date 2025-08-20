<?php

namespace Modules\DriverCommission\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\DriverCommission\app\Http\Requests\StoreDriverCommissionRequest;
use Modules\DriverCommission\app\Http\Requests\UpdateDriverCommissionRequest;
use Modules\DriverCommission\app\Http\Resources\DriverCommissionCollection;
use Modules\DriverCommission\app\Http\Resources\DriverCommissionResource;
use Modules\DriverCommission\app\Models\DriverCommission;
use Modules\DriverCommission\app\Repositories\DriverCommissionRepository;

class DriverCommissionController extends Controller
{
    public function __construct(
        private DriverCommissionRepository $commissionRepository
    ) {
        // $this->middleware('auth');
        // $this->middleware('permission:view-driver-commissions')->only(['index', 'show', 'getData']);
        // $this->middleware('permission:create-driver-commissions')->only(['create', 'store']);
        // $this->middleware('permission:edit-driver-commissions')->only(['edit', 'update']);
        // $this->middleware('permission:delete-driver-commissions')->only(['destroy']);
        // $this->middleware('permission:manage-payments')->only(['markAsPaid', 'markAsFailed', 'bulkUpdatePayment']);
    }

    /**
     * Display the driver commission management page
     */
    public function index(): View
    {
        // Get statistics for dashboard
        $stats = $this->getDashboardStats();
        
        // Get drivers for filter dropdown
        $drivers = User::whereHas('roles', function($q) {
                $q->where('name', 'driver');
            })
            ->where('status', 'active')
            ->select('id', 'first_name', 'email', 'phone')
            ->orderBy('first_name')
            ->get();


        return view('drivercommission::index', compact('stats', 'drivers'));
    }

    /**
     * Get data for AJAX DataTable
     */
    public function getData(Request $request): JsonResponse
    {
        try {
            $filters = $request->validate([
                'search' => 'nullable|string|max:255',
                'driver_id' => 'nullable|integer|exists:users,id',
                'commission_type' => 'nullable|in:per_ride,daily_bonus,weekly_bonus,monthly_bonus,referral_bonus,penalty',
                'payment_status' => 'nullable|in:pending,processing,paid,failed,cancelled',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'view_mode' => 'nullable|in:pending,paid,failed,all',
                'per_page' => 'nullable|integer|min:10|max:100',
            ]);

            $perPage = $filters['per_page'] ?? 15;

            // Build query based on filters
            $query = DriverCommission::with(['driver:id,name,email,phone', 'rideAssignment:id,pickup_location,dropoff_location']);

            // Apply search filter
            if (!empty($filters['search'])) {
                $query->whereHas('driver', function ($q) use ($filters) {
                    $q->where('name', 'LIKE', "%{$filters['search']}%")
                      ->orWhere('email', 'LIKE', "%{$filters['search']}%");
                })->orWhere('payment_reference', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('description', 'LIKE', "%{$filters['search']}%");
            }

            // Apply other filters
            if (!empty($filters['driver_id'])) {
                $query->where('driver_id', $filters['driver_id']);
            }

            if (!empty($filters['commission_type'])) {
                $query->where('commission_type', $filters['commission_type']);
            }

            if (!empty($filters['payment_status'])) {
                $query->where('payment_status', $filters['payment_status']);
            }

            // Apply view mode filter
            if (!empty($filters['view_mode']) && $filters['view_mode'] !== 'all') {
                $query->where('payment_status', $filters['view_mode']);
            }

            // Apply date range filter
            if (!empty($filters['start_date'])) {
                $query->whereDate('earning_date', '>=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $query->whereDate('earning_date', '<=', $filters['end_date']);
            }

            // Order by latest first
            $query->orderBy('earning_date', 'desc')->orderBy('created_at', 'desc');

            $commissions = $query->paginate($perPage);

            // Generate HTML for table rows
            $html = $this->generateTableRowsHtml($commissions->items());

            return response()->json([
                'success' => true,
                'html' => $html,
                'data' => new DriverCommissionCollection($commissions),
                'total' => $commissions->total(),
                'from' => $commissions->firstItem(),
                'to' => $commissions->lastItem(),
                'current_page' => $commissions->currentPage(),
                'last_page' => $commissions->lastPage(),
            ]);

        } catch (\Exception $e) {
            Log::error('Driver Commission getData error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load commission data',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a new driver commission
     */
    public function store(StoreDriverCommissionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Calculate commission amount if not provided
            if (!isset($data['commission_amount'])) {
                $data['commission_amount'] = ($data['base_fare'] * $data['commission_rate']) / 100;
            }

            // Calculate total earning
            $data['total_earning'] = ($data['commission_amount'] ?? 0) + 
                                   ($data['bonus_amount'] ?? 0) - 
                                   ($data['penalty_amount'] ?? 0);

            $commission = $this->commissionRepository->create($data);

            return response()->json([
                'success' => true,
                'message' => 'কমিশন সফলভাবে তৈরি হয়েছে',
                'data' => new DriverCommissionResource($commission->load(['driver', 'rideAssignment'])),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Driver Commission store error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'কমিশন তৈরি করতে সমস্যা হয়েছে',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display a specific driver commission
     */
    public function show(DriverCommission $driverCommission): JsonResponse
    {
        $driverCommission->load(['driver', 'rideAssignment']);

        return response()->json([
            'success' => true,
            'data' => new DriverCommissionResource($driverCommission),
        ]);
    }

    /**
     * Update a driver commission
     */
    public function update(UpdateDriverCommissionRequest $request, DriverCommission $driverCommission): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Recalculate commission amount if base fare or rate changed
            if (isset($data['base_fare']) || isset($data['commission_rate'])) {
                $baseFare = $data['base_fare'] ?? $driverCommission->base_fare;
                $commissionRate = $data['commission_rate'] ?? $driverCommission->commission_rate;
                $data['commission_amount'] = ($baseFare * $commissionRate) / 100;
            }

            // Recalculate total earning
            $commissionAmount = $data['commission_amount'] ?? $driverCommission->commission_amount;
            $bonusAmount = $data['bonus_amount'] ?? $driverCommission->bonus_amount ?? 0;
            $penaltyAmount = $data['penalty_amount'] ?? $driverCommission->penalty_amount ?? 0;
            
            $data['total_earning'] = $commissionAmount + $bonusAmount - $penaltyAmount;

            $updatedCommission = $this->commissionRepository->update($driverCommission->id, $data);

            return response()->json([
                'success' => true,
                'message' => 'কমিশন সফলভাবে আপডেট হয়েছে',
                'data' => new DriverCommissionResource($updatedCommission->load(['driver', 'rideAssignment'])),
            ]);

        } catch (\Exception $e) {
            Log::error('Driver Commission update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'কমিশন আপডেট করতে সমস্যা হয়েছে',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove a driver commission
     */
    public function destroy(DriverCommission $driverCommission): JsonResponse
    {
        try {
            // Check if commission is already paid
            if ($driverCommission->isPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'পরিশোধিত কমিশন মুছে ফেলা যায় না',
                ], 422);
            }

            $this->commissionRepository->delete($driverCommission->id);

            return response()->json([
                'success' => true,
                'message' => 'কমিশন সফলভাবে মুছে ফেলা হয়েছে',
            ]);

        } catch (\Exception $e) {
            Log::error('Driver Commission delete error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'কমিশন মুছে ফেলতে সমস্যা হয়েছে',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark commission as paid
     */
    public function markAsPaid(Request $request, DriverCommission $driverCommission): JsonResponse
    {
        try {
            $validated = $request->validate([
                'payment_method' => 'required|string|max:255',
                'payment_reference' => 'nullable|string|max:255|unique:driver_commissions,payment_reference,' . $driverCommission->id,
            ]);

            if ($driverCommission->isPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'এই কমিশনটি ইতিমধ্যে পরিশোধিত',
                ], 422);
            }

            $success = $driverCommission->markAsPaid(
                $validated['payment_method'],
                $validated['payment_reference'] ?? null
            );

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'পেমেন্ট আপডেট করতে সমস্যা হয়েছে',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'কমিশন সফলভাবে পরিশোধিত হিসেবে মার্ক করা হয়েছে',
                'data' => new DriverCommissionResource($driverCommission->fresh()->load(['driver', 'rideAssignment'])),
            ]);

        } catch (\Exception $e) {
            Log::error('Mark as paid error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'পেমেন্ট আপডেট করতে সমস্যা হয়েছে',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark commission as failed
     */
    public function markAsFailed(Request $request, DriverCommission $driverCommission): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            if ($driverCommission->isPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'পরিশোধিত কমিশন ব্যর্থ হিসেবে মার্ক করা যায় না',
                ], 422);
            }

            $success = $driverCommission->markAsFailed($validated['reason']);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'পেমেন্ট স্ট্যাটাস আপডেট করতে সমস্যা হয়েছে',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'কমিশন ব্যর্থ হিসেবে মার্ক করা হয়েছে',
                'data' => new DriverCommissionResource($driverCommission->fresh()->load(['driver', 'rideAssignment'])),
            ]);

        } catch (\Exception $e) {
            Log::error('Mark as failed error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'পেমেন্ট স্ট্যাটাস আপডেট করতে সমস্যা হয়েছে',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Bulk update payment status
     */
    public function bulkUpdatePayment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'commission_ids' => 'required|array|min:1',
                'commission_ids.*' => 'integer|exists:driver_commissions,id',
                'payment_status' => 'required|in:paid,failed,processing',
                'payment_method' => 'nullable|string|max:255',
                'payment_reference' => 'nullable|string|max:255',
                'failure_reason' => 'nullable|string|max:500',
            ]);

            $additionalData = [];

            if ($validated['payment_status'] === 'paid') {
                $additionalData['payment_date'] = now();
                if (!empty($validated['payment_method'])) {
                    $additionalData['payment_method'] = $validated['payment_method'];
                }
                if (!empty($validated['payment_reference'])) {
                    $additionalData['payment_reference'] = $validated['payment_reference'];
                }
            }

            if ($validated['payment_status'] === 'failed' && !empty($validated['failure_reason'])) {
                $additionalData['metadata->failure_reason'] = $validated['failure_reason'];
                $additionalData['metadata->failed_at'] = now()->toISOString();
            }

            $updatedCount = $this->commissionRepository->bulkUpdatePaymentStatus(
                $validated['commission_ids'],
                $validated['payment_status'],
                $additionalData
            );

            $statusText = match($validated['payment_status']) {
                'paid' => 'পরিশোধিত',
                'failed' => 'ব্যর্থ',
                'processing' => 'প্রক্রিয়াধীন',
                default => $validated['payment_status']
            };

            return response()->json([
                'success' => true,
                'message' => "সফলভাবে {$updatedCount} টি কমিশনের স্ট্যাটাস '{$statusText}' এ আপডেট করা হয়েছে",
                'data' => [
                    'updated_count' => $updatedCount,
                    'payment_status' => $validated['payment_status'],
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk update payment error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'বাল্ক পেমেন্ট আপডেট করতে সমস্যা হয়েছে',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Export commissions data
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'commission_ids' => 'nullable|array',
                'commission_ids.*' => 'integer|exists:driver_commissions,id',
                'format' => 'nullable|in:excel,csv,pdf',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $format = $validated['format'] ?? 'excel';

            // Build query for export
            $query = DriverCommission::with(['driver', 'rideAssignment']);

            if (!empty($validated['commission_ids'])) {
                $query->whereIn('id', $validated['commission_ids']);
            }

            if (!empty($validated['start_date'])) {
                $query->whereDate('earning_date', '>=', $validated['start_date']);
            }

            if (!empty($validated['end_date'])) {
                $query->whereDate('earning_date', '<=', $validated['end_date']);
            }

            $commissions = $query->orderBy('earning_date', 'desc')->get();

            // Generate export file (you can implement actual export logic here)
            $filename = 'driver_commissions_' . now()->format('Y_m_d_H_i_s') . '.' . $format;

            return response()->json([
                'success' => true,
                'message' => 'এক্সপোর্ট ফাইল তৈরি হচ্ছে...',
                'data' => [
                    'filename' => $filename,
                    'download_url' => route('admin.driver-commissions.download', ['filename' => $filename]),
                    'total_records' => $commissions->count(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Export commissions error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'এক্সপোর্ট করতে সমস্যা হয়েছে',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats(): array
    {
        try {
            $today = now();
            $thisMonth = $today->copy()->startOfMonth();

            return [
                'total_commissions' => DriverCommission::count(),
                'total_earnings' => DriverCommission::sum('total_earning'),
                'pending_payments' => DriverCommission::where('payment_status', 'pending')->count(),
                'pending_amount' => DriverCommission::where('payment_status', 'pending')->sum('total_earning'),
                'paid_payments' => DriverCommission::where('payment_status', 'paid')->count(),
                'paid_amount' => DriverCommission::where('payment_status', 'paid')->sum('total_earning'),
                'active_drivers' => User::where('role', 'driver')
                    ->where('status', 'active')
                    ->whereHas('driverCommissions', function ($q) use ($thisMonth) {
                        $q->whereDate('earning_date', '>=', $thisMonth);
                    })->count(),
                'todays_commissions' => DriverCommission::whereDate('earning_date', $today)->count(),
                'this_month_earnings' => DriverCommission::whereDate('earning_date', '>=', $thisMonth)->sum('total_earning'),
            ];
        } catch (\Exception $e) {
            Log::error('Dashboard stats error: ' . $e->getMessage());
            
            return [
                'total_commissions' => 0,
                'total_earnings' => 0,
                'pending_payments' => 0,
                'pending_amount' => 0,
                'paid_payments' => 0,
                'paid_amount' => 0,
                'active_drivers' => 0,
                'todays_commissions' => 0,
                'this_month_earnings' => 0,
            ];
        }
    }

    /**
     * Generate HTML for table rows
     */
    private function generateTableRowsHtml($commissions): string
    {
        if (empty($commissions)) {
            return '<tr><td colspan="11" class="text-center py-4">
                        <div class="empty-state">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">কোন কমিশন পাওয়া যায়নি</h5>
                            <p class="text-muted">ফিল্টার পরিবর্তন করুন অথবা নতুন কমিশন যোগ করুন।</p>
                        </div>
                    </td></tr>';
        }

        $html = '';
        foreach ($commissions as $index => $commission) {
            $isPositiveEarning = $commission->total_earning >= 0;
            $earningClass = $isPositiveEarning ? 'text-success' : 'text-danger';
            
            $html .= '<tr>
                <td><input type="checkbox" class="form-check-input commission-checkbox" value="' . $commission->id . '"></td>
                <td>' . ($index + 1) . '</td>
                <td>
                    <strong>' . ($commission->driver->name ?? 'অজ্ঞাত ড্রাইভার') . '</strong><br>
                    <small class="text-muted driver-info">' . ($commission->driver->email ?? 'N/A') . '</small><br>
                    <small class="text-muted driver-info">' . ($commission->driver->phone ?? 'N/A') . '</small>
                </td>
                <td>' . $this->getCommissionTypeBadge($commission->commission_type) . '</td>
                <td class="earnings-amount">৳' . number_format($commission->base_fare, 2) . '</td>
                <td>' . number_format($commission->commission_rate, 1) . '%</td>
                <td class="earnings-amount ' . $earningClass . '">৳' . number_format($commission->total_earning, 2) . '</td>
                <td>' . $this->getPaymentStatusBadge($commission->payment_status) . '</td>
                <td>
                    <strong>' . $commission->earning_date->format('d M, Y') . '</strong><br>
                    <small class="date-info">' . $this->getRelativeDate($commission->earning_date) . '</small>
                </td>
                <td>' . ($commission->payment_date ? 
                    '<strong>' . $commission->payment_date->format('d M, Y') . '</strong><br>
                     <small class="date-info">' . ($commission->payment_method ?? 'N/A') . '</small>' : 
                    '<span class="text-muted">-</span>') . '
                </td>
                <td>
                    <button class="btn btn-sm btn-gradient-primary editBtn" data-id="' . $commission->id . '" title="সম্পাদনা">
                        <i class="fas fa-edit"></i>
                    </button>';
            
            if ($commission->payment_status !== 'paid') {
                $html .= '<button class="btn btn-sm btn-gradient-success paymentBtn" data-id="' . $commission->id . '" data-action="paid" title="পরিশোধিত মার্ক">
                            <i class="fas fa-check"></i>
                          </button>
                          <button class="btn btn-sm btn-gradient-danger paymentBtn" data-id="' . $commission->id . '" data-action="failed" title="ব্যর্থ মার্ক">
                            <i class="fas fa-times"></i>
                          </button>';
            } else {
                $html .= '<button class="btn btn-sm btn-gradient-info" disabled title="ইতিমধ্যে পরিশোধিত">
                            <i class="fas fa-check-circle"></i>
                          </button>';
            }
            
            $html .= '</td></tr>';
        }

        return $html;
    }

    /**
     * Get commission type badge HTML
     */
    private function getCommissionTypeBadge(string $type): string
    {
        $badges = [
            'per_ride' => '<span class="badge commission-type-badge bg-primary">প্রতি রাইড</span>',
            'daily_bonus' => '<span class="badge commission-type-badge bg-success">দৈনিক বোনাস</span>',
            'weekly_bonus' => '<span class="badge commission-type-badge bg-info">সাপ্তাহিক বোনাস</span>',
            'monthly_bonus' => '<span class="badge commission-type-badge bg-warning">মাসিক বোনাস</span>',
            'referral_bonus' => '<span class="badge commission-type-badge bg-secondary">রেফারেল বোনাস</span>',
            'penalty' => '<span class="badge commission-type-badge bg-danger">জরিমানা</span>'
        ];

        return $badges[$type] ?? '<span class="badge commission-type-badge bg-light">' . $type . '</span>';
    }

    /**
     * Get payment status badge HTML
     */
    private function getPaymentStatusBadge(string $status): string
    {
        $badges = [
            'pending' => '<span class="badge payment-status-badge bg-warning">বকেয়া</span>',
            'processing' => '<span class="badge payment-status-badge bg-info">প্রক্রিয়াধীন</span>',
            'paid' => '<span class="badge payment-status-badge bg-success">পরিশোধিত</span>',
            'failed' => '<span class="badge payment-status-badge bg-danger">ব্যর্থ</span>',
            'cancelled' => '<span class="badge payment-status-badge bg-secondary">বাতিল</span>'
        ];

        return $badges[$status] ?? '<span class="badge payment-status-badge bg-light">' . $status . '</span>';
    }

    /**
     * Get relative date string
     */
    private function getRelativeDate(Carbon $date): string
    {
        $diffInDays = $date->diffInDays(now());
        
        if ($diffInDays === 0) {
            return 'আজ';
        } elseif ($diffInDays === 1) {
            return 'গতকাল';
        } else {
            return $diffInDays . ' দিন আগে';
        }
    }
}