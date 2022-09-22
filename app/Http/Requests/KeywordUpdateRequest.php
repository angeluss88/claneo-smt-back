<?php

namespace App\Http\Requests;

use App\Models\Keyword;
use Illuminate\Validation\Rule;

/**
 * @property Keyword $keyword
 */
class KeywordUpdateRequest extends BaseRequest
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
            'keyword' => [
                'string',
                'max:255',
                Rule::unique('keywords')->ignore($this->getIgnoreId()),
            ],
            'search_volume' => 'integer',
            'search_volume_clustered' => 'integer',
            'current_ranking_url' => 'max:255',
            'featured_snippet_keyword' => [
                Rule::in(['ja', 'nein', 'yes', 'no', 'Ja', 'Nein', 'Yes', 'No']),
            ],
            'featured_snippet_owned' => [
                Rule::in(['ja', 'nein', 'yes', 'no', 'Ja', 'Nein', 'Yes', 'No']),
            ],
            'current_ranking_position' => [
                Rule::in(array_merge(range(1, 100), [ 'Nicht in Top 100', 'nicht in Top 100', 'Not in Top 100', 'not in Top 100'])),
            ],
            'search_intention' => [
                Rule::in(['informational', 'Informational', 'transaktional', 'Transaktional', 'transactional',
                    'Informational/transaktional', 'informational/transaktional', 'informational/transactional', 'Informational/transactional',
                    'navigational', 'Navigational']),
            ],
        ];
    }

    protected function getIgnoreId(): int
    {
        $data = explode('/', $this->url());
        $id = array_pop($data);

        if(array_pop($data) !== 'keywords') {
            abort(522, 'non-keywords param');
        }

        return (int) $id;
    }
}
