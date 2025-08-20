<?php

namespace Modules\DriverCommission\app\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Modules\DriverCommission\app\Models\DriverCommission;
use Modules\DriverCommission\app\Models\DriverEarningsSummary;
use Modules\DriverCommission\app\Repositories\DriverCommissionRepositoryInterface;

class DriverCommissionRepository implements DriverCommissionRepositoryInterface
{
    public function __construct(
        protected DriverCommission $model,
        protected DriverEarningsSummary $summaryModel
    ) {}

    public function find(int $id): ?DriverCommission
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): DriverCommission
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): DriverCommission
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): DriverCommission
    {
        $commission = $this->findOrFail($id);
        $commission->update($data);
        return $commission->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function getDriverCommissions(
        int $driverId,
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = $this->model->byDriver($driverId)
            ->with(['driver', 'rideAssignment']);

        // Apply filters
        if (!empty($filters['commission_type'])) {
            $query->byCommissionType($filters['commission_type']);
        }

        if (!empty($filters['payment_status'])) {
            $query->byPaymentStatus($filters['payment_status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->dateRange($filters['start_date'], $filters['end_date']);
        }

        if (!empty($filters['date_filter'])) {
            match ($filters['date_filter']) {
                'today' => $query->today(),
                'this_week' => $query->thisWeek(),
                'this_month' => $query->thisMonth(),
                default => null,
            };
        }

        return $query->orderBy('earning_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getDriverEarningsSummary(
        int $driverId,
        string $summaryType = 'daily',
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->summaryModel->getDriverSummary($driverId, $summaryType, $startDate, $endDate)
            ->paginate($perPage);
    }

    public function getTotalEarnings(
        int $driverId,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): float {
        return DriverCommission::totalEarningsForDriver($driverId, $startDate, $endDate);
    }

    public function getPendingEarnings(int $driverId): float
    {
        return DriverCommission::pendingEarningsForDriver($driverId);
    }

    public function getCommissionsByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->byPaymentStatus($status)
            ->with(['driver', 'rideAssignment'])
            ->orderBy('earning_date', 'desc')
            ->paginate($perPage);
    }

    public function getCommissionsByDateRange(
        Carbon $startDate,
        Carbon $endDate,
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = $this->model->dateRange($startDate, $endDate)
            ->with(['driver', 'rideAssignment']);

        if (!empty($filters['driver_id'])) {
            $query->byDriver($filters['driver_id']);
        }

        if (!empty($filters['commission_type'])) {
            $query->byCommissionType($filters['commission_type']);
        }

        if (!empty($filters['payment_status'])) {
            $query->byPaymentStatus($filters['payment_status']);
        }

        return $query->orderBy('earning_date', 'desc')
            ->paginate($perPage);
    }

    public function bulkUpdatePaymentStatus(
        array $commissionIds,
        string $status,
        array $additionalData = []
    ): int {
        $updateData = array_merge(['payment_status' => $status], $additionalData);

        if ($status === 'paid') {
            $updateData['payment_date'] = now();
        }

        return $this->model->whereIn('id', $commissionIds)
            ->update($updateData);
    }

    public function getDriverStats(int $driverId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = $this->model->byDriver($driverId);

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $commissions = $query->get();

        return [
            'total_commissions' => $commissions->count(),
            'total_earnings' => $commissions->sum('total_earning'),
            'total_commission_amount' => $commissions->sum('commission_amount'),
            'total_bonus' => $commissions->sum('bonus_amount'),
            'total_penalty' => $commissions->sum('penalty_amount'),
            'pending_payments' => $commissions->where('payment_status', 'pending')->count(),
            'paid_payments' => $commissions->where('payment_status', 'paid')->count(),
            'pending_amount' => $commissions->where('payment_status', 'pending')->sum('total_earning'),
            'paid_amount' => $commissions->where('payment_status', 'paid')->sum('total_earning'),
        ];
    }

    public function createDriverCommission(array $data): DriverCommission
    {
        // Calculate commission amount if not provided
        if (!isset($data['commission_amount']) && isset($data['base_fare']) && isset($data['commission_rate'])) {
            $data['commission_amount'] = ($data['base_fare'] * $data['commission_rate']) / 100;
        }

        // Calculate total earning
        $data['total_earning'] = ($data['commission_amount'] ?? 0) + 
                                ($data['bonus_amount'] ?? 0) - 
                                ($data['penalty_amount'] ?? 0);

        return $this->create($data);
    }

    public function getTopEarningDrivers(
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
        int $limit = 10
    ): Collection {
        $query = $this->model->selectRaw('driver_id, SUM(total_earning) as total_earnings')
            ->with('driver:id,name,email')
            ->groupBy('driver_id');

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->orderBy('total_earnings', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getEarningsTrend(
        int $driverId,
        string $period = 'daily',
        int $days = 30
    ): Collection {
        $startDate = now()->subDays($days);
        $endDate = now();

        return $this->model->byDriver($driverId)
            ->dateRange($startDate, $endDate)
            ->selectRaw('DATE(earning_date) as date, SUM(total_earning) as total_earnings')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
