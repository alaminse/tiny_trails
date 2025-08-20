<?php

namespace Modules\RideAssignment\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRideAssignmentRequest extends FormRequest
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
        return [
            'driver_id' => 'required|exists:users,id',
            'parent_id' => 'required|exists:users,id',
            'kid_id' => 'nullable|exists:kids,id',
            'subscription_id' => 'nullable|exists:subscriptions,id',
            'ride_title' => 'required|string|max:255',
            'pickup_location' => 'required|string|max:500',
            'dropoff_location' => 'required|string|max:500',
            'pickup_latitude' => 'nullable|numeric|between:-90,90',
            'pickup_longitude' => 'nullable|numeric|between:-180,180',
            'dropoff_latitude' => 'nullable|numeric|between:-90,90',
            'dropoff_longitude' => 'nullable|numeric|between:-180,180',
            'ride_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|date_format:H:i',
            'estimated_dropoff_time' => 'nullable|date_format:H:i|after:pickup_time',
            'recurring_days' => 'nullable|array',
            'recurring_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_recurring' => 'boolean',
            'recurring_end_date' => 'nullable|date|after:ride_date|required_if:is_recurring,true',
            'distance_km' => 'nullable|numeric|min:0',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
            'ride_fare' => 'required|numeric|min:0',
            'driver_commission' => 'nullable|numeric|min:0',
            'platform_fee' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::in(['assigned', 'accepted', 'in_progress', 'completed', 'cancelled', 'no_show'])],
            'ride_type' => ['required', Rule::in(['one_time', 'daily', 'weekly', 'custom'])],
            'special_instructions' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'driver_id.required' => 'Driver is required.',
            'driver_id.exists' => 'Selected driver does not exist.',
            'parent_id.required' => 'Parent is required.',
            'parent_id.exists' => 'Selected parent does not exist.',
            'kid_id.exists' => 'Selected kid does not exist.',
            'subscription_id.exists' => 'Selected subscription does not exist.',
            'ride_title.required' => 'Ride title is required.',
            'ride_title.max' => 'Ride title cannot exceed 255 characters.',
            'pickup_location.required' => 'Pickup location is required.',
            'pickup_location.max' => 'Pickup location cannot exceed 500 characters.',
            'dropoff_location.required' => 'Dropoff location is required.',
            'dropoff_location.max' => 'Dropoff location cannot exceed 500 characters.',
            'pickup_latitude.between' => 'Pickup latitude must be between -90 and 90.',
            'pickup_longitude.between' => 'Pickup longitude must be between -180 and 180.',
            'dropoff_latitude.between' => 'Dropoff latitude must be between -90 and 90.',
            'dropoff_longitude.between' => 'Dropoff longitude must be between -180 and 180.',
            'ride_date.required' => 'Ride date is required.',
            'ride_date.after_or_equal' => 'Ride date cannot be in the past.',
            'pickup_time.required' => 'Pickup time is required.',
            'pickup_time.date_format' => 'Pickup time must be in HH:MM format.',
            'estimated_dropoff_time.date_format' => 'Estimated dropoff time must be in HH:MM format.',
            'estimated_dropoff_time.after' => 'Estimated dropoff time must be after pickup time.',
            'recurring_days.array' => 'Recurring days must be an array.',
            'recurring_days.*.in' => 'Invalid recurring day selected.',
            'recurring_end_date.after' => 'Recurring end date must be after ride date.',
            'recurring_end_date.required_if' => 'Recurring end date is required for recurring rides.',
            'distance_km.min' => 'Distance must be greater than or equal to 0.',
            'estimated_duration_minutes.min' => 'Duration must be at least 1 minute.',
            'ride_fare.required' => 'Ride fare is required.',
            'ride_fare.min' => 'Ride fare must be greater than or equal to 0.',
            'driver_commission.min' => 'Driver commission must be greater than or equal to 0.',
            'platform_fee.min' => 'Platform fee must be greater than or equal to 0.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
            'ride_type.required' => 'Ride type is required.',
            'ride_type.in' => 'Invalid ride type selected.',
            'special_instructions.max' => 'Special instructions cannot exceed 1000 characters.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_recurring')) {
            $this->merge([
                'is_recurring' => $this->boolean('is_recurring'),
            ]);
        }

        // Set default status if not provided
        if (!$this->has('status')) {
            $this->merge([
                'status' => 'assigned',
            ]);
        }

        // Set default ride type if not provided
        if (!$this->has('ride_type')) {
            $this->merge([
                'ride_type' => 'one_time',
            ]);
        }
    }
}