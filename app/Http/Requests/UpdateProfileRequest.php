<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $isDriver = $user->hasRole('driver');

        $rules = [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'phone' => 'sometimes|string|max:20',
            'dob' => 'sometimes|date|before:today',
            'gender' => 'sometimes|in:male,female,other',
            'height_cm' => 'sometimes|numeric|min:0|max:300',
            'weight_kg' => 'sometimes|numeric|min:0|max:500',
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
            'address' => 'sometimes|string|max:500',
            'country_id' => 'sometimes|exists:countries,id',
            'state_id' => 'sometimes|exists:states,id',
            'city_id' => 'sometimes|exists:cities,id',
            'password' => 'sometimes|string|min:8|confirmed',
        ];

        // Add driver-specific validation rules
        if ($isDriver) {
            $rules = array_merge($rules, [
                'driving_license_number' => 'sometimes|string|max:255',
                'driving_license_expiry' => 'sometimes|date|after:today',
                'driving_license_image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
                'car_model' => 'sometimes|string|max:255',
                'car_make' => 'sometimes|string|max:255',
                'car_year' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
                'car_color' => 'sometimes|string|max:100',
                'car_plate_number' => 'sometimes|string|max:255',
                'car_image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
                'face_image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'dob' => 'date of birth',
            'height_cm' => 'height',
            'weight_kg' => 'weight',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
            'driving_license_number' => 'driving license number',
            'driving_license_expiry' => 'driving license expiry date',
            'driving_license_image' => 'driving license image',
            'car_model' => 'car model',
            'car_make' => 'car make',
            'car_year' => 'car year',
            'car_color' => 'car color',
            'car_plate_number' => 'car plate number',
            'car_image' => 'car image',
            'face_image' => 'face image',
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already taken by another user.',
            'dob.before' => 'Date of birth must be in the past.',
            'driving_license_expiry.after' => 'Driving license expiry date must be in the future.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
