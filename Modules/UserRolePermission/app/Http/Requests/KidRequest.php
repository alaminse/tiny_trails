<?php

namespace Modules\UserRolePermission\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KidRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */

    public function rules(): array
    {
        $kidId = $this->route('kid')?->id;

        return [
            'first_name'                        => ['required', 'string', 'max:255'],
            'middle_name'                       => ['nullable', 'string', 'max:255'],
            'last_name'                         => ['nullable', 'string', 'max:255'],
            'dob'                               => ['required', 'date'],
            'gender'                            => ['required', 'in:Boy,Girl,Prefer not to say'],
            'height_cm'                         => ['nullable', 'numeric', 'min:0'],
            'weight_kg'                         => ['nullable', 'numeric', 'min:0'],
            'school_name'                       => ['nullable', 'string', 'max:255'],
            'school_address'                    => ['nullable', 'string', 'max:500'],
            'emergency_contacts'                => ['nullable', 'array'],
            'emergency_contacts.*.name'         => ['required_with:emergency_contacts', 'string', 'max:255'],
            'emergency_contacts.*.relationship' => ['required_with:emergency_contacts', 'string', 'max:255'],
            'emergency_contacts.*.phone'        => ['required_with:emergency_contacts', 'string', 'max:20'],
            'user_id'                           => ['nullable', 'exists:users,id'],
            'photo'                             => ['nullable', 'image', 'max:2048'],
            'pickup_location'                   => ['required', 'string', 'max:500'],
            'pickup_latitude'                   => ['required', 'numeric', 'between:-90,90'],
            'pickup_longitude'                  => ['required', 'numeric', 'between:-180,180'],
            'pickup_location_id'                => ['nullable', 'exists:locations,id'],
            'dropoff_location'                  => ['required', 'string', 'max:500'],
            'dropoff_latitude'                  => ['required', 'numeric', 'between:-90,90'],
            'dropoff_longitude'                 => ['required', 'numeric', 'between:-180,180'],
            'dropoff_location_id'               => ['nullable', 'exists:locations,id'],
            'distance_between_locations'        => ['nullable', 'numeric'],
            'hair_color'                        => ['nullable', 'string', 'max:100'],
            'eye_color'                         => ['nullable', 'string', 'max:100'],
            'birthmarks'                        => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
