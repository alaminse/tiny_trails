<?php

namespace Modules\UserRolePermission\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */

    public function rules(): array
    {
        $roleId = $this->route('role')?->id; // null on create

        return [
            'name' => 'required|string|max:255|unique:roles,name' . ($roleId ? ',' . $roleId : ''),
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
