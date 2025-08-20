<?php

// Bulk Update Request
namespace Modules\DriverCommission\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkUpdatePaymentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'commission_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'commission_ids.*' => [
                'required',
                'integer',
                'exists:driver_commissions,id',
            ],
            'payment_status' => [
                'required',
                Rule::in(['pending', 'processing', 'paid', 'failed', 'cancelled']),
            ],
            'payment_method' => [
                'nullable',
                'string',
                'max:255',
                'required_if:payment_status,paid',
            ],
            'payment_reference' => [
                'nullable',
                'string',
                'max:255',
            ],
            'failure_reason' => [
                'nullable',
                'string',
                'max:500',
                'required_if:payment_status,failed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'commission_ids.required' => 'Please select at least one commission to update.',
            'commission_ids.*.exists' => 'One or more selected commissions do not exist.',
            'payment_method.required_if' => 'Payment method is required when marking as paid.',
            'failure_reason.required_if' => 'Failure reason is required when marking as failed.',
        ];
    }
}