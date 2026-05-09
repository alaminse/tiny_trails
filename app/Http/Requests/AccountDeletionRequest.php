<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountDeletionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email', 'max:255'],
            'reason'   => ['required', 'string', 'in:no-longer-use,privacy-concerns,better-alternative,too-many-notifications,other'],
            'comments' => ['nullable', 'string', 'max:2000'],
            'consent'  => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Please enter your registered email address.',
            'email.email'       => 'Please enter a valid email address.',
            'reason.required'   => 'Please select a reason for deletion.',
            'reason.in'         => 'Please select a valid reason.',
            'consent.accepted'  => 'You must confirm that you understand this action is permanent.',
        ];
    }
}
