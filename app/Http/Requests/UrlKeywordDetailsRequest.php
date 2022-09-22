<?php

namespace App\Http\Requests;

/**
 * @property int $url_id
 * @property string|null $import_date
 */
class UrlKeywordDetailsRequest extends BaseRequest
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
            'url_id' => 'required',
            'count' => 'required',
            'page' => 'required',
        ];
    }
}
