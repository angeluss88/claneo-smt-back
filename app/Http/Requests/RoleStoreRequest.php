<?php

namespace App\Http\Requests;

class RoleStoreRequest extends BaseRequest
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
            'name' => 'required|unique:roles,name|string|max:255',
            'description' => 'required|string|max:255',
        ];
    }
}
