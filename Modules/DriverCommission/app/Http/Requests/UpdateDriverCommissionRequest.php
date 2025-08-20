<?php

namespace Modules\DriverCommission\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $commissionId = $this->route('driver_commission')?->id ?? $this->route('id');
        
        return [
            'driver_id' => [
                'sometimes',
                'integer',
                Rule::exists('users', 'id')->where('role', 'driver'),
            ],
            'ride_assignment_id' => [
                'nullable',
                'integer',
                'exists:ride_assignments,id',
            ],
            'base_fare' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:99999.99',
            ],
            'commission_rate' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:100',
            ],
            'commission_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999.99',
            ],
            'bonus_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999.99',
            ],
            'penalty_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999.99',
            ],
            'commission_type' => [
                'sometimes',
                Rule::in(['per_ride', 'daily_bonus', 'weekly_bonus', 'monthly_bonus', 'referral_bonus', 'penalty']),
            ],
            'payment_status' => [
                'sometimes',
                Rule::in(['pending', 'processing', 'paid', 'failed', 'cancelled']),
            ],
            'earning_date' => [
                'sometimes',
                'date',
                'before_or_equal:today',
            ],
            'payment_date' => [
                'nullable',
                'date',
                'after_or_equal:earning_date',
            ],
            'payment_method' => [
                'nullable',
                'string',
                'max:255',
            ],
            'payment_reference' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('driver_commissions', 'payment_reference')->ignore($commissionId),
            ],
            'bonus_type' => [
                'nullable',
                'string',
                'max:255',
            ],
            'penalty_type' => [
                'nullable',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'metadata' => [
                'nullable',
                'array',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'driver_id.exists' => 'The selected driver does not exist or is not a valid driver.',
            'commission_rate.max' => 'Commission rate cannot exceed 100%.',
            'earning_date.before_or_equal' => 'Earning date cannot be in the future.',
            'payment_date.after_or_equal' => 'Payment date must be on or after the earning date.',
            'payment_reference.unique' => 'This payment reference has already been used.',
        ];
    }
}