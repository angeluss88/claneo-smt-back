<?php

namespace App\Http\Requests;

use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use function PHPUnit\Framework\exactly;

class UrlUpdateRequest extends FormRequest
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
     * @throws Exception
     */
    public function rules(): array
    {
        return [
            'url' => [
                'string',
                'max:255',
                Rule::unique('urls')->ignore($this->getIgnoreId()),
            ],
            'status' => [
                Rule::in(['301', '200', 301, 200, 'NEW', 'new', 'NEU', 'neu']),
            ],
            'project_id'            => 'integer',
            'main_category'         => 'string|max:255',
            'sub_category'          => 'string|max:255',
            'sub_category2'         => 'string|max:255',
            'sub_category3'         => 'string|max:255',
            'sub_category4'         => 'string|max:255',
            'sub_category5'         => 'string|max:255',
            'page_type'             => 'string|max:255',
            'keywords'              => 'array',
        ];
    }

    protected function getIgnoreId(): int
    {
        $data = explode('/', $this->url());
        $id = array_pop($data);

        if(array_pop($data) !== 'urls') {
            abort(522, 'non-urls param');
        }

        return (int) $id;
    }
}
