<?php

namespace App\Http\Requests;

use App\Models\SeoEvent;
use Illuminate\Validation\Rule;

class SeoEventUpdateRequest extends BaseRequest
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
            'title' => 'string|max:255',
            'description' => 'string|max:255',
            'entity_type' => [
                'required_with:entity_id',
                Rule::in([SeoEvent::PROJECT_TYPE, SeoEvent::URL_TYPE]),
            ],
            'entity_id' => 'required_with:entity_type',
            'date' => 'date_format:' . SeoEvent::DATE_FORMAT,
        ];
    }
}
