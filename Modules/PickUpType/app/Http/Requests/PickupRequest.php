<?php

namespace Modules\PickUpType\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PickupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->pickuptype->id ?? null; 

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('pickup_types', 'name')->ignore($id)],
            'amount' => ['required', 'numeric', 'min:0'],
            'min_notice_minutes' => ['required', 'integer', 'min:0'],
            'requires_instant_notification' => ['required', 'boolean'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
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
