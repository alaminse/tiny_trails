<?php

namespace Modules\RideAssignment\app\Repositories;

use Modules\RideAssignment\app\Models\RideAssignment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Models\User;

class RideAssignmentRepository
{
    protected $model;

    public function __construct(RideAssignment $model)
    {
        $this->model = $model;
    }

    /**
     * Get all ride assignments
     */
    public function all(array $relations = []): Collection
    {
        return $this->model->with($relations)->latest('ride_date')->get();
    }

    /**
     * Get active ride assignments
     */
    public function active(array $relations = []): Collection
    {
        return $this->model->with($relations)->active()->latest('ride_date')->get();
    }

    /**
     * Get paginated ride assignments
     */
    public function paginate(int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        return $this->model->with($relations)->latest('ride_date')->paginate($perPage);
    }

    /**
     * Find ride assignment by ID
     */
    public function findById(int $id, array $relations = []): ?RideAssignment
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Create new ride assignment
     */
    public function create(array $data): RideAssignment
    {
        // Calculate commission if not provided
        if (!isset($data['driver_commission']) && isset($data['ride_fare'])) {
            $commissionRate = 15; // Default 15%
            $data['driver_commission'] = ($data['ride_fare'] * $commissionRate) / 100;
            $data['platform_fee'] = $data['ride_fare'] - $data['driver_commission'];
        }

        $ride = $this->model->create($data);

        // Handle recurring rides
        if ($ride->is_recurring && $ride->recurring_days) {
            $this->createRecurringRides($ride);
        }

        return $ride;
    }

    /**
     * Update ride assignment
     */
    public function update(int $id, array $data): bool
    {
        $ride = $this->findById($id);
        if (!$ride) {
            return false;
        }

        // Recalculate commission if fare is updated
        if (isset($data['ride_fare']) && $data['ride_fare'] != $ride->ride_fare) {
            $commissionRate = 15; // Default 15%
            $data['driver_commission'] = ($data['ride_fare'] * $commissionRate) / 100;
            $data['platform_fee'] = $data['ride_fare'] - $data['driver_commission'];
        }

        return $ride->update($data);
    }

    /**
     * Delete ride assignment (soft delete)
     */
    public function delete(int $id): bool
    {
        $ride = $this->findById($id);
        if (!$ride) {
            return false;
        }
        return $ride->delete();
    }

    /**
     * Restore soft deleted ride assignment
     */
    public function restore(int $id): bool
    {
        $ride = $this->model->withTrashed()->find($id);
        if (!$ride) {
            return false;
        }
        return $ride->restore();
    }

    /**
     * Permanently delete ride assignment
     */
    public function forceDelete(int $id): bool
    {
        $ride = $this->model->withTrashed()->find($id);
        if (!$ride) {
            return false;
        }
        return $ride->forceDelete();
    }

    /**
     * Get trashed ride assignments
     */
    public function getTrashed(array $relations = []): Collection
    {
        return $this->model->onlyTrashed()->with($relations)->latest('ride_date')->get();
    }

    /**
     * Get rides for a specific driver
     */
    public function getByDriver(int $driverId, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->forDriver($driverId)
            ->latest('ride_date')
            ->get();
    }

    /**
     * Get rides for a specific parent
     */
    public function getByParent(int $parentId, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->forParent($parentId)
            ->latest('ride_date')
            ->get();
    }

    /**
     * Get today's rides
     */
    public function getTodaysRides(array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->today()
            ->orderBy('pickup_time')
            ->get();
    }

    /**
     * Get upcoming rides
     */
    public function getUpcomingRides(array $relations = [], int $days = 7): Collection
    {
        return $this->model->with($relations)
            ->whereBetween('ride_date', [now(), now()->addDays($days)])
            ->orderBy('ride_date')
            ->orderBy('pickup_time')
            ->get();
    }

    /**
     * Get rides by status
     */
    public function getByStatus(string $status, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->byStatus($status)
            ->latest('ride_date')
            ->get();
    }

    /**
     * Get rides by date range
     */
    public function getByDateRange(string $startDate, string $endDate, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->byDateRange($startDate, $endDate)
            ->orderBy('ride_date')
            ->orderBy('pickup_time')
            ->get();
    }

    /**
     * Search rides
     */
    public function search(string $term, array $relations = []): Collection
    {
        return $this->model->with($relations)
            ->where(function ($query) use ($term) {
                $query->where('ride_title', 'like', "%{$term}%")
                      ->orWhere('pickup_location', 'like', "%{$term}%")
                      ->orWhere('dropoff_location', 'like', "%{$term}%")
                      ->orWhere('special_instructions', 'like', "%{$term}%")
                      ->orWhereHas('driver', function ($q) use ($term) {
                          $q->where('name', 'like', "%{$term}%");
                      })
                      ->orWhereHas('parent', function ($q) use ($term) {
                          $q->where('name', 'like', "%{$term}%");
                      });
            })
            ->latest('ride_date')
            ->get();
    }

    /**
     * Get rides for DataTable
     */
    public function getDataTableData(bool $trashed = false): Collection
    {
        $query = $trashed ? $this->model->onlyTrashed() : $this->model->whereNull('deleted_at');
        
        return $query->with(['driver', 'parent', 'kid'])
            ->latest('ride_date')
            ->get();
    }

    /**
     * Accept ride
     */
    public function acceptRide(int $id): bool
    {
        $ride = $this->findById($id);
        if (!$ride || !$ride->canBeAccepted()) {
            return false;
        }

        $ride->accept();
        return true;
    }

    /**
     * Start ride
     */
    public function startRide(int $id): bool
    {
        $ride = $this->findById($id);
        if (!$ride || !$ride->canBeStarted()) {
            return false;
        }

        $ride->start();
        return true;
    }

    /**
     * Complete ride
     */
    public function completeRide(int $id): bool
    {
        $ride = $this->findById($id);
        if (!$ride || !$ride->canBeCompleted()) {
            return false;
        }

        $ride->complete();
        return true;
    }

    /**
     * Cancel ride
     */
    public function cancelRide(int $id, string $reason = null, int $cancelledBy = null): bool
    {
        $ride = $this->findById($id);
        if (!$ride || !$ride->canBeCancelled()) {
            return false;
        }

        $ride->cancel($reason, $cancelledBy);
        return true;
    }

    /**
     * Mark ride as no show
     */
    public function markAsNoShow(int $id): bool
    {
        $ride = $this->findById($id);
        if (!$ride) {
            return false;
        }

        $ride->markAsNoShow();
        return true;
    }

    /**
     * Get driver statistics
     */
    public function getDriverStats(int $driverId, string $period = 'today'): array
    {
        $query = $this->model->forDriver($driverId);

        switch ($period) {
            case 'today':
                $query->today();
                break;
            case 'week':
                $query->whereBetween('ride_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('ride_date', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
        }

        $rides = $query->get();

        return [
            'total_rides' => $rides->count(),
            'completed_rides' => $rides->where('status', 'completed')->count(),
            'cancelled_rides' => $rides->where('status', 'cancelled')->count(),
            'in_progress_rides' => $rides->where('status', 'in_progress')->count(),
            'pending_rides' => $rides->where('status', 'assigned')->count(),
            'total_earnings' => $rides->where('status', 'completed')->sum('driver_commission'),
            'total_distance' => $rides->where('status', 'completed')->sum('distance_km'),
        ];
    }

    /**
     * Get system statistics
     */
    public function getSystemStats(): array
    {
        $today = now()->format('Y-m-d');
        
        return [
            'total_rides' => $this->model->count(),
            'todays_rides' => $this->model->whereDate('ride_date', $today)->count(),
            'active_rides' => $this->model->active()->count(),
            'completed_rides' => $this->model->completed()->count(),
            'cancelled_rides' => $this->model->cancelled()->count(),
            'total_revenue' => $this->model->completed()->sum('ride_fare'),
            'total_commission_paid' => $this->model->completed()->sum('driver_commission'),
        ];
    }

    /**
     * Create recurring rides
     */
    protected function createRecurringRides(RideAssignment $originalRide): void
    {
        if (!$originalRide->is_recurring || !$originalRide->recurring_days || !$originalRide->recurring_end_date) {
            return;
        }

        $currentDate = Carbon::parse($originalRide->ride_date)->addDay();
        $endDate = Carbon::parse($originalRide->recurring_end_date);
        $recurringDays = $originalRide->recurring_days;

        while ($currentDate->lte($endDate)) {
            $dayName = strtolower($currentDate->format('l'));
            
            if (in_array($dayName, $recurringDays)) {
                $rideData = $originalRide->toArray();
                unset($rideData['id'], $rideData['created_at'], $rideData['updated_at'], $rideData['deleted_at']);
                
                $rideData['ride_date'] = $currentDate->format('Y-m-d');
                $rideData['status'] = 'assigned';
                $rideData['accepted_at'] = null;
                $rideData['started_at'] = null;
                $rideData['completed_at'] = null;
                $rideData['cancelled_at'] = null;
                $rideData['cancelled_by'] = null;
                
                $this->model->create($rideData);
            }
            
            $currentDate->addDay();
        }
    }

    /**
     * Bulk assign rides
     */
    public function bulkAssign(array $rideIds, int $driverId): int
    {
        return $this->model->whereIn('id', $rideIds)
            ->where('status', 'assigned')
            ->update(['driver_id' => $driverId]);
    }

    /**
     * Bulk cancel rides
     */
    public function bulkCancel(array $rideIds, string $reason = null, int $cancelledBy = null): int
    {
        return $this->model->whereIn('id', $rideIds)
            ->whereIn('status', ['assigned', 'accepted'])
            ->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
                'cancelled_by' => $cancelledBy,
            ]);
    }

    /**
     * Get available drivers for a ride
     */
    public function getAvailableDrivers(string $rideDate, string $pickupTime): Collection
    {
        // Get drivers who don't have conflicting rides
        $conflictingDriverIds = $this->model
            ->where('ride_date', $rideDate)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($pickupTime) {
                $query->where('pickup_time', $pickupTime)
                      ->orWhereBetween('pickup_time', [
                          Carbon::parse($pickupTime)->subHour()->format('H:i:s'),
                          Carbon::parse($pickupTime)->addHour()->format('H:i:s')
                      ]);
            })
            ->pluck('driver_id');

        return User::where('role', 'driver')
            ->where('is_active', true)
            ->whereNotIn('id', $conflictingDriverIds)
            ->get();
    }
}