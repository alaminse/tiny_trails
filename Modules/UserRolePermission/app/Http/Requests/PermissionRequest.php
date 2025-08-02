<?php

namespace Modules\UserRolePermission\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    public function rules(): array
    {
        $permissionId = $this->route('permission')?->id; // null on create

        return [
            'name' => 'required|string|max:255|unique:permissions,name' . ($permissionId ? ',' . $permissionId : ''),
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
