<?php

namespace Modules\DriverCommission\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterDriverCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'driver_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'commission_type' => [
                'nullable',
                Rule::in(['per_ride', 'daily_bonus', 'weekly_bonus', 'monthly_bonus', 'referral_bonus', 'penalty']),
            ],
            'payment_status' => [
                'nullable',
                Rule::in(['pending', 'processing', 'paid', 'failed', 'cancelled']),
            ],
            'start_date' => [
                'nullable',
                'date',
                'before_or_equal:end_date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
                'before_or_equal:today',
            ],
            'date_filter' => [
                'nullable',
                Rule::in(['today', 'yesterday', 'this_week', 'last_week', 'this_month', 'last_month']),
            ],
            'sort_by' => [
                'nullable',
                Rule::in(['earning_date', 'total_earning', 'commission_amount', 'created_at']),
            ],
            'sort_direction' => [
                'nullable',
                Rule::in(['asc', 'desc']),
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.before_or_equal' => 'Start date must be before or equal to end date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'end_date.before_or_equal' => 'End date cannot be in the future.',
            'per_page.max' => 'Maximum 100 items per page allowed.',
        ];
    }

    public function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'sort_by' => $this->sort_by ?? 'earning_date',
            'sort_direction' => $this->sort_direction ?? 'desc',
            'per_page' => $this->per_page ?? 15,
        ]);

        // Handle date filters
        if ($this->date_filter && !$this->start_date && !$this->end_date) {
            $dates = $this->getDateRangeForFilter($this->date_filter);
            $this->merge($dates);
        }
    }

    private function getDateRangeForFilter(string $filter): array
    {
        return match ($filter) {
            'today' => [
                'start_date' => now()->toDateString(),
                'end_date' => now()->toDateString(),
            ],
            'yesterday' => [
                'start_date' => now()->subDay()->toDateString(),
                'end_date' => now()->subDay()->toDateString(),
            ],
            'this_week' => [
                'start_date' => now()->startOfWeek()->toDateString(),
                'end_date' => now()->endOfWeek()->toDateString(),
            ],
            'last_week' => [
                'start_date' => now()->subWeek()->startOfWeek()->toDateString(),
                'end_date' => now()->subWeek()->endOfWeek()->toDateString(),
            ],
            'this_month' => [
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->endOfMonth()->toDateString(),
            ],
            'last_month' => [
                'start_date' => now()->subMonth()->startOfMonth()->toDateString(),
                'end_date' => now()->subMonth()->endOfMonth()->toDateString(),
            ],
            default => [],
        };
    }
}