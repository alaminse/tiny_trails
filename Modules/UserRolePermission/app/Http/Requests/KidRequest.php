<?php

namespace Modules\UserRolePermission\App\Http\Requests;

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
            'first_name'       => ['required', 'string', 'max:255'],
            'last_name'        => ['required', 'string', 'max:255'],
            'dob'              => ['required', 'date'],
            'gender'           => ['required', 'in:male,female,other'],
            'height_cm'        => ['nullable', 'string', 'min:0'],
            'weight_kg'        => ['nullable', 'numeric', 'min:0'],
            'school_name'      => ['nullable', 'string', 'max:255'],
            'school_address'   => ['nullable', 'string', 'max:500'],
            'emergency_contact'=> ['nullable', 'string', 'max:255'],
            'user_id'          => ['required', 'exists:users,id'],
            'photo'            => ['nullable', 'image', 'max:2048'],
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
