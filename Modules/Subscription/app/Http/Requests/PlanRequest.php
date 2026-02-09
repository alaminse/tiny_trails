<?php

namespace Modules\Subscription\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // plan id (only available on update)
        $planId = $this->route('plan')->id ?? $this->route('plan');

        return [
            'pickup_type_id' => 'required|exists:pickup_types,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plans', 'name')->ignore($planId),
            ],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'interval' => ['required', Rule::in(['day', 'week', 'month', 'year'])],
            'interval_count' => 'required|integer|min:1',
            'features' => 'nullable|string',
            'plan_tier' => 'nullable|string|max:50',
            'iot_level' => 'nullable|string|max:50',
            'includes_hardware' => 'nullable|boolean',
            'hardware_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'pickup_type_id.required' => 'Pickup type is required.',
            'pickup_type_id.exists' => 'Selected pickup type does not exist.',
            'name.required' => 'Plan name is required.',
            'name.unique' => 'This plan name already exists.',
            'price.required' => 'Plan price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price must be greater than or equal to 0.',
            'sell_price.required' => 'Sell price is required.',
            'sell_price.numeric' => 'Sell price must be a valid number.',
            'sell_price.min' => 'Sell price must be greater than or equal to 0.',
            'currency.required' => 'Currency is required.',
            'currency.size' => 'Currency must be exactly 3 characters.',
            'interval.required' => 'Billing interval is required.',
            'interval.in' => 'Invalid billing interval selected.',
            'interval_count.required' => 'Interval count is required.',
            'interval_count.integer' => 'Interval count must be a valid integer.',
            'interval_count.min' => 'Interval count must be at least 1.',
            'plan_tier.max' => 'Plan tier must not exceed 50 characters.',
            'iot_level.max' => 'IoT level must not exceed 50 characters.',
            'hardware_price.numeric' => 'Hardware price must be a valid number.',
            'hardware_price.min' => 'Hardware price must be greater than or equal to 0.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status value.',
            'sort_order.integer' => 'Sort order must be a valid integer.',
            'sort_order.min' => 'Sort order must be greater than or equal to 0.',
        ];
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        // Default sort order
        if (!$this->has('sort_order')) {
            $this->merge([
                'sort_order' => 0,
            ]);
        }

        // Convert boolean fields
        if ($this->has('includes_hardware')) {
            $this->merge([
                'includes_hardware' => filter_var($this->includes_hardware, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
