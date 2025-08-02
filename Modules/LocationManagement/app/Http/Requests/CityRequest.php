<?php

namespace Modules\LocationManagement\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
         $id = $this->city->id ?? null;

        return [
            'state_id'    => ['required', 'exists:states,id'],
            'name'          => ['required', 'string', 'max:255', Rule::unique('cities', 'name')->ignore($id)],
            'status'        => ['required', Rule::in(['active', 'inactive'])],
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
