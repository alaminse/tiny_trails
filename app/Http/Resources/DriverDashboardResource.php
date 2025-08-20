<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class DriverDashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'driver_info' => [
                'id' => $this->id,
                'name' => $this->first_name . ' ' . $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'status' => $this->status,
                'joined_date' => $this->created_at->toDateString(),
            ],
            'today_summary' => [
                'date' => Carbon::today()->toDateString(),
                'total_rides' => $this->getTodayRidesCount(),
                'completed_rides' => $this->getTodayCompletedRides(),
                'earnings' => $this->getTodayEarnings(),
                'status' => $this->getDriverStatus(),
            ],
            'earnings_summary' => [
                'today' => $this->getTodayEarnings(),
                'weekly' => $this->getWeeklyEarnings(),
                'monthly' => $this->getMonthlyEarnings(),
                'pending_payments' => $this->getPendingPayments(),
            ],
        ];
    }
}