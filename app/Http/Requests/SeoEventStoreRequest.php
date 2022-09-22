<?php

namespace App\Http\Requests;

use App\Models\SeoEvent;
use Illuminate\Validation\Rule;

class SeoEventStoreRequest extends BaseRequest
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
            'title' => 'required|string|max:255',
            'description' => 'string|max:255',
            'entity_type' => [
                'required',
                Rule::in([SeoEvent::PROJECT_TYPE, SeoEvent::URL_TYPE]),
            ],
            'entity_id' => 'required',
            'date' => 'date_format:' . SeoEvent::DATE_FORMAT,
        ];
    }
}
