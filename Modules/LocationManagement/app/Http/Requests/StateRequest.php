<?php

namespace Modules\LocationManagement\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
         $id = $this->state->id ?? null;

        return [
            'country_id'    => ['required', 'exists:countries,id'],
            'name'          => ['required', 'string', 'max:255', Rule::unique('states', 'name')->ignore($id)],
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
