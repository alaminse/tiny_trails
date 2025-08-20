<?php

namespace Modules\DriverCommission\app\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\DriverCommission\app\Models\DriverCommission;

class DriverCommissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'manager', 'driver']);
    }

    public function view(User $user, DriverCommission $commission): bool
    {
        // Admin and managers can view any commission
        if ($user->hasRole(['admin', 'manager'])) {
            return true;
        }

        // Drivers can only view their own commissions
        return $user->hasRole('driver') && $user->id === $commission->driver_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'manager']);
    }

    public function update(User $user, DriverCommission $commission): bool
    {
        // Only admin and managers can update commissions
        // Paid commissions cannot be updated
        return $user->hasRole(['admin', 'manager']) && !$commission->isPaid();
    }

    public function delete(User $user, DriverCommission $commission): bool
    {
        // Only admin can delete commissions
        // Paid commissions cannot be deleted
        return $user->hasRole('admin') && !$commission->isPaid();
    }

    public function bulkUpdate(User $user): bool
    {
        return $user->hasRole(['admin', 'manager']);
    }

    public function viewAnalytics(User $user): bool
    {
        return $user->hasRole(['admin', 'manager']);
    }
}
