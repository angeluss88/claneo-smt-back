<?php

namespace App\Http\Requests;

use App\Services\GoogleAnalyticsService;
use Illuminate\Validation\Rule;

/**
 * @property int $project_id
 * @property string|null $import_date
 * @property string $metric
 */
class TimeLineDataRequest extends BaseRequest
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
            'project_id' => 'required',
            'metric' => [
                'required',
                Rule::in(array_merge(GoogleAnalyticsService::GA_METRICS, GoogleAnalyticsService::GSC_METRICS)),
            ],
        ];
    }
}
