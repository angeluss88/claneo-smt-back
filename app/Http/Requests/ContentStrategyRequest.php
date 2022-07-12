<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int|null $count
 * @property int|null $page
 * @property int|null $project_id
 * @property int|null $import_id
 * @property string|null $import_date
 * @property string|null $url
 * @property string|null $keyword
 */
class ContentStrategyRequest extends FormRequest
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
            //
        ];
    }
}