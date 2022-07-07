<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectStoreRequest extends FormRequest
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
            'domain' => 'required|unique:projects,domain|string|max:255',
            'client_id' => 'required_without:client|exists:clients,id',
            'client' => 'required_without:client_id|exists:clients,name',
            'ga_property_id' => 'max:20',
            'ua_property_id' => 'max:20',
            'ua_view_id' => 'max:20',
            'strategy'  => [
                'required',
                'max:255',
                Rule::in([ Project::GA_STRATEGY, Project::UA_STRATEGY, Project::NO_EXPAND_STRATEGY]),
            ],
            'expand_gsc'  => [
                'required',
                Rule::in([ 0,1 ]),
            ],
        ];
    }
}
