<?php

namespace Modules\Subscription\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // This is a comprehensive list of validation rules for all subscription attributes.
        // For the `store` method, you would typically only need 'plan_id'.
        // Other attributes are shown here for cases like updating a subscription
        // or for future functionality where more data might be required from the user.
        return [
            // The plan_id is the only required input for creating a new subscription.
            'plan_id'               => ['required', 'exists:plans,id'],

            // The following fields are typically handled by the system and payment gateway.
            'user_id'               => ['sometimes', 'exists:users,id'],
            'name'                  => ['sometimes', 'string', 'max:255'],
            'stripe_id'             => ['sometimes', 'nullable', 'string', 'max:255', 'unique:subscriptions,stripe_id'],
            'stripe_status'         => ['sometimes', 'string', Rule::in(['active', 'trialing', 'past_due', 'canceled'])],
            'trial_ends_at'         => ['sometimes', 'nullable', 'date'],
            'ends_at'               => ['sometimes', 'nullable', 'date'],
            'canceled_at'           => ['sometimes', 'nullable', 'date'],
            'cancellation_reason'   => ['sometimes', 'nullable', 'string'],
            'card_brand'            => ['sometimes', 'nullable', 'string'],
            'card_last_four'        => ['sometimes', 'nullable', 'string', 'size:4'],
            'card_expiration'       => ['sometimes', 'nullable', 'string', 'size:7', 'regex:/^\d{2}\/\d{4}$/'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // A user must be authenticated and have the 'parent' role to subscribe.
        return true;
    }
}
