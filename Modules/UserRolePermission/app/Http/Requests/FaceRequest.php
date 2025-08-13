<?php

namespace Modules\UserRolePermission\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */

    public function rules(): array
    {
        return [
            'embedding' => 'required|string',
            'faceImage' => 'required|file|image', 
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
