<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Keyword
 *
 * @property int $id
 * @property string $keyword
 * @property int $search_volume
 * @property int|null $search_volume_clustered
 * @property string|null $current_ranking_url
 * @property int|null $featured_snippet_keyword
 * @property int|null $featured_snippet_owned
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $search_intention
 * @property int|null $import_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\URL[] $urls
 * @property-read int|null $urls_count
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCurrentRankingUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereFeaturedSnippetKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereFeaturedSnippetOwned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereImportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereSearchIntention($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereSearchVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereSearchVolumeClustered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Keyword extends Model
{
    use HasFactory;

    const KEYWORD = 'Keyword';
    const SEARCH_VOLUME = 'Suchvolumen';
    const SV_CLUSTERED = 'SV clustered';
    const CURRENT_RANKING_URL = 'Aktuelle Ranking URL';
    const FEATURED_SNIPPET_KW = 'Featured Snippet für Keyword vorhanden';
    const FEATURED_SNIPPET_OWNED = 'Featured Snippet für @project.domain vorhanden';
    const CURRENT_RANKING_POSITION = 'Aktuelle Ranking Position @project.domain';
    const CURRENT_RANKING_POSITION_2 = 'Kunde';
    const SEARCH_INTENTION = 'Suchintention';

    const KEYWORD_EN = 'Keyword';
    const SEARCH_VOLUME_EN = 'Search volume';
    const SV_CLUSTERED_EN = 'Seach volume clustered';
    const CURRENT_RANKING_URL_EN = 'Current ranking URL';
    const FEATURED_SNIPPET_KW_EN = 'featured snippet keyword';
    const FEATURED_SNIPPET_OWNED_EN = 'featured snippet owned';
    const CURRENT_RANKING_POSITION_EN = 'Current ranking position';
    const SEARCH_INTENTION_EN = 'Search intention';

    /**
     * @return BelongsToMany
     */
    public function urls(): BelongsToMany
    {
        return $this->belongsToMany(URL::class, 'url_keyword', 'url_id', 'keyword_id')
            ->withPivot('current_ranking_position', 'clicks', 'impressions', 'ctr');
    }

}
