<?php

namespace Modules\UserRolePermission\App\Http\Requests;

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
            $rules = array_merge($rules, [
                'driving_license_number' => ['required', 'string', 'max:255'],
                'driving_license_expiry' => ['required', 'date', 'after:today'],
                'driving_license_image'  => [$this->isMethod('post') ? 'required' : 'nullable', 'image', 'max:2048'],
                'car_model'              => ['required', 'string', 'max:255'],
                'car_make'               => ['required', 'string', 'max:255'],
                'car_year'               => ['required', 'integer', 'digits:4', 'min:1900', 'max:' . date('Y')],
                'car_color'              => ['required', 'string', 'max:50'],
                'car_plate_number'       => ['required', 'string', 'max:50'],
                'car_image'              => [$this->isMethod('post') ? 'required' : 'nullable', 'image', 'max:2048'],
            ]);
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
