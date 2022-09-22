<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UserUpdateRequest extends BaseRequest
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
            'first_name' => 'string|max:100',
            'last_name' => 'string|max:100',
            'email' => [
                'email',
                Rule::unique('users')->ignore($this->user->id),
            ],
            'privacy_policy_flag' => 'boolean',
            'password' => 'string',
            'roles' => 'array',
            'client' => 'string',
            'client_id' => 'integer',
        ];
    }
}
