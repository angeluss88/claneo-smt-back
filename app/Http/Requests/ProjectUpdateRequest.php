<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Validation\Rule;

/**
 * @property Project $project
 */
class ProjectUpdateRequest extends BaseRequest
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
            'domain' => [
                'string',
                'max:255',
                Rule::unique('projects')->ignore($this->project->id),
            ],
            'client_id' => 'exists:clients,id',
            'client' => 'exists:clients,name',
            'ga_property_id' => 'max:20',
            'ua_property_id' => 'max:20',
            'ua_view_id' => 'max:20',
            'strategy'  => [
                'max:255',
                Rule::in([ Project::GA_STRATEGY, Project::UA_STRATEGY, Project::NO_EXPAND_STRATEGY]),
            ],
            'expand_gsc'  => [
                Rule::in([ 0,1 ]),
            ],

        ];
    }
}
