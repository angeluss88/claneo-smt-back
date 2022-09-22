<?php

namespace App\Http\Requests;

class RoleUpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'unique:roles,name|string|max:255',
            'description' => 'string|max:255',
        ];
    }
}
