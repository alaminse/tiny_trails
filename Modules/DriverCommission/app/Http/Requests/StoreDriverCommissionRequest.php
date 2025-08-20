<?php

namespace Modules\DriverCommission\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreDriverCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handle authorization in controller or policy
    }

    public function rules(): array
    {
        return [
            'driver_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'driver'),
            ],
            'ride_assignment_id' => [
                'nullable',
                'integer',
                'exists:ride_assignments,id',
            ],
            'base_fare' => [
                'required',
                'numeric',
                'min:0',
                'max:99999.99',
            ],
            'commission_rate' => [
                'required',
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
                'required',
                Rule::in(['per_ride', 'daily_bonus', 'weekly_bonus', 'monthly_bonus', 'referral_bonus', 'penalty']),
            ],
            'payment_status' => [
                'sometimes',
                Rule::in(['pending', 'processing', 'paid', 'failed', 'cancelled']),
            ],
            'earning_date' => [
                'required',
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
                'unique:driver_commissions,payment_reference',
            ],
            'bonus_type' => [
                'nullable',
                'string',
                'max:255',
                'required_if:commission_type,daily_bonus,weekly_bonus,monthly_bonus,referral_bonus',
            ],
            'penalty_type' => [
                'nullable',
                'string',
                'max:255',
                'required_if:commission_type,penalty',
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
            'base_fare.required' => 'Base fare is required.',
            'commission_rate.required' => 'Commission rate is required.',
            'commission_rate.max' => 'Commission rate cannot exceed 100%.',
            'earning_date.before_or_equal' => 'Earning date cannot be in the future.',
            'payment_date.after_or_equal' => 'Payment date must be on or after the earning date.',
            'payment_reference.unique' => 'This payment reference has already been used.',
            'bonus_type.required_if' => 'Bonus type is required for bonus commission types.',
            'penalty_type.required_if' => 'Penalty type is required for penalty commission type.',
        ];
    }

    public function prepareForValidation(): void
    {
        // Set default payment status if not provided
        if (!$this->has('payment_status')) {
            $this->merge(['payment_status' => 'pending']);
        }

        // Calculate commission amount if not provided
        if (!$this->has('commission_amount') && $this->has('base_fare') && $this->has('commission_rate')) {
            $commissionAmount = ($this->base_fare * $this->commission_rate) / 100;
            $this->merge(['commission_amount' => round($commissionAmount, 2)]);
        }
    }
}
