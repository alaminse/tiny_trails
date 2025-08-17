<?php

namespace Modules\Subscription\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends FormRequest
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
        $subscriptionId = $this->route('subscription')->id ?? $this->route('subscription');

        return [
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'name' => 'required|string|max:255',
            'stripe_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('subscriptions', 'stripe_id')->ignore($subscriptionId),
            ],
            'stripe_status' => 'required|string|max:255',
            'trial_ends_at' => 'nullable|date|after:now',
            'ends_at' => 'nullable|date',
            'canceled_at' => 'nullable|date',
            'cancellation_reason' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'card_brand' => 'nullable|string|max:50',
            'card_last_four' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
            'card_expiration' => 'nullable|string|max:10',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'User is required.',
            'user_id.exists' => 'Selected user does not exist.',
            'plan_id.required' => 'Plan is required.',
            'plan_id.exists' => 'Selected plan does not exist.',
            'name.required' => 'Subscription name is required.',
            'name.string' => 'Subscription name must be a string.',
            'name.max' => 'Subscription name cannot exceed 255 characters.',
            'stripe_id.unique' => 'This Stripe ID already exists.',
            'stripe_status.required' => 'Stripe status is required.',
            'trial_ends_at.date' => 'Trial end date must be a valid date.',
            'trial_ends_at.after' => 'Trial end date must be in the future.',
            'ends_at.date' => 'End date must be a valid date.',
            'canceled_at.date' => 'Cancellation date must be a valid date.',
            'card_last_four.size' => 'Card last four digits must be exactly 4 digits.',
            'card_last_four.regex' => 'Card last four digits must contain only numbers.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Defaults for create requests
        if ($this->isMethod('post')) {
            if (!$this->has('name') || empty($this->name)) {
                $this->merge([
                    'name' => 'default',
                ]);
            }

            if (!$this->has('stripe_status') || empty($this->stripe_status)) {
                $this->merge([
                    'stripe_status' => 'active',
                ]);
            }
        }
    }
}
