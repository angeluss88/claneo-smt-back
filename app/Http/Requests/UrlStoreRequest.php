<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UrlStoreRequest extends BaseRequest
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
            'url'                   => 'required|unique:urls,url|string|max:255',
            'project_id'            => 'integer',
            'status' => [
                'required',
                Rule::in(['301', '200', 301, 200, 'NEW', 'new', 'NEU', 'neu']),
            ],
            'main_category'         => 'required|string|max:255',
            'sub_category'          => 'string|max:255',
            'sub_category2'         => 'string|max:255',
            'sub_category3'         => 'string|max:255',
            'sub_category4'         => 'string|max:255',
            'sub_category5'         => 'string|max:255',
            'page_type'             => 'string|max:255',
        ];
    }
}
