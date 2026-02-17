<?php

namespace Modules\UserRolePermission\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? null;
        $role = $this->input('role');

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
            // Password required only on create
            'password'   => [$this->isMethod('post') ? 'required' : 'nullable', 'string', 'min:6'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'dob'        => ['nullable', 'date', 'before:today'],
            'gender'     => ['nullable', 'in:male,female,other'],
            'height_cm'  => ['nullable', 'numeric', 'min:0'],
            'weight_kg'  => ['nullable', 'numeric', 'min:0'],
            'address'    => ['nullable', 'string'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'state_id'   => ['nullable', 'exists:states,id'],
            'city_id'    => ['nullable', 'exists:cities,id'],
            'status'     => ['required', 'in:active,inactive'],
            'role'       => ['required', 'exists:roles,name'],
        ];

        if ($role === 'driver') {
            $driverRules = [
                // License Details
                'driving_license_number' => ['required', 'string', 'max:255'],
                'licence_card_number'     => ['nullable', 'string', 'max:255'],
                'licence_type'            => ['required', 'in:full,provisional,probationary'],
                'driving_license_expiry'  => ['required', 'date', 'after:today'],
                'driving_license_image'   => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],

                // License Address
                'licence_address_line_1' => ['nullable', 'string', 'max:255'],
                'licence_address_line_2' => ['nullable', 'string', 'max:255'],
                'licence_city'           => ['nullable', 'string', 'max:100'],
                'licence_state'          => ['nullable', 'string', 'max:100'],
                'licence_postal_code'    => ['nullable', 'string', 'max:20'],
                'licence_country'        => ['nullable', 'string', 'max:100'],

                // Car Details
                'car_make'         => ['required', 'string', 'max:100'],
                'car_model'        => ['required', 'string', 'max:100'],
                'car_year'         => ['required', 'integer', 'digits:4', 'min:1900', 'max:' . (date('Y') + 1)],
                'car_color'        => ['required', 'string', 'max:50'],
                'car_plate_number' => ['required', 'string', 'max:20'],
                'car_image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],

                // Other Qualifications
                'wwc_card_number'       => ['nullable', 'string', 'max:255'],
                'wwc_expiry_date'       => ['nullable', 'date', 'after:today'],
                'wwc_card_image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
                'police_clearance_ref'  => ['nullable', 'string', 'max:255'],
                'police_clearance_image'=> ['nullable', 'image', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
                'other_qualifications'  => ['nullable', 'string', 'max:2000'],
                'face_image'            => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],

                // Driver Status
                'is_verified'    => ['required', 'boolean'],
                'driver_status'  => ['required', 'in:active,inactive,suspended'],
            ];
        }

        return $rules;
    }


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
