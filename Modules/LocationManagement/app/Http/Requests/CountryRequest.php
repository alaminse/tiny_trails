<?php

namespace Modules\LocationManagement\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CountryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
         $id = $this->country->id ?? null;

        return [
            'name'      => ['required', 'string', 'max:255', Rule::unique('countries', 'name')->ignore($id)],
            'status'    => ['required', Rule::in(['active', 'inactive'])],
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
