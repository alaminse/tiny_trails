<?php

namespace Modules\DriverCommission\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'driver_id' => $this->resource['driver_id'] ?? null,
            'period' => $this->resource['period'] ?? null,
            'start_date' => $this->resource['start_date'] ?? null,
            'end_date' => $this->resource['end_date'] ?? null,
            
            // Commission Statistics
            'total_commissions' => $this->resource['total_commissions'],
            'total_earnings' => number_format($this->resource['total_earnings'], 2),
            'total_commission_amount' => number_format($this->resource['total_commission_amount'], 2),
            'total_bonus' => number_format($this->resource['total_bonus'], 2),
            'total_penalty' => number_format($this->resource['total_penalty'], 2),
            
            // Payment Statistics
            'pending_payments' => $this->resource['pending_payments'],
            'paid_payments' => $this->resource['paid_payments'],
            'pending_amount' => number_format($this->resource['pending_amount'], 2),
            'paid_amount' => number_format($this->resource['paid_amount'], 2),
            
            // Calculated Metrics
            'payment_completion_rate' => $this->calculatePaymentCompletionRate(),
            'average_earning_per_commission' => $this->calculateAverageEarning(),
            
            // Driver Information
            'driver' => $this->when(
                isset($this->resource['driver']),
                $this->resource['driver']
            ),
        ];
    }

    private function calculatePaymentCompletionRate(): float
    {
        $total = $this->resource['total_commissions'];
        $paid = $this->resource['paid_payments'];
        
        return $total > 0 ? round(($paid / $total) * 100, 2) : 0;
    }

    private function calculateAverageEarning(): float
    {
        $total = $this->resource['total_commissions'];
        $earnings = $this->resource['total_earnings'];
        
        return $total > 0 ? round($earnings / $total, 2) : 0;
    }
}