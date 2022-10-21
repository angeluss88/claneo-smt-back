<?php

namespace App\Http\Requests;

/**
 * @property int|null $project_id
 * @property resource $file
 */
class ImportUrlsRequest extends BaseRequest
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
            'file' => 'required|file|mimes:csv,txt',
            'project_id' => 'required|integer|exists:projects,id',
        ];
    }
}
