<?php

namespace App\Http\Requests;

use App\Services\GoogleAnalyticsService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $url_id
 * @property string|null $import_date
 * @property string $metric
 */
class UrlDetailsRequest extends FormRequest
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
            'metric' => [
                'required',
                Rule::in(array_merge(GoogleAnalyticsService::GA_METRICS, GoogleAnalyticsService::GSC_METRICS)),
            ],
        ];
    }
}
