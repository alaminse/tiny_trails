<?php

namespace Modules\DriverCommission\app\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Modules\DriverCommission\app\Models\DriverCommission;

interface DriverCommissionRepositoryInterface
{
    public function find(int $id): ?DriverCommission;
    
    public function findOrFail(int $id): DriverCommission;
    
    public function create(array $data): DriverCommission;
    
    public function update(int $id, array $data): DriverCommission;
    
    public function delete(int $id): bool;
    
    public function getDriverCommissions(
        int $driverId,
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator;
    
    public function getTotalEarnings(
        int $driverId,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): float;
    
    public function getPendingEarnings(int $driverId): float;
    
    public function getCommissionsByStatus(string $status, int $perPage = 15): LengthAwarePaginator;
    
    public function bulkUpdatePaymentStatus(
        array $commissionIds,
        string $status,
        array $additionalData = []
    ): int;
    
    public function getDriverStats(int $driverId, ?Carbon $startDate = null, ?Carbon $endDate = null): array;
}