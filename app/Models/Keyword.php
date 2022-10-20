<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $search_intention
 * @property int|null $import_id
 * @property-read Collection|URL[] $urls
 * @property-read int|null $urls_count
 * @method static Builder|Keyword newModelQuery()
 * @method static Builder|Keyword newQuery()
 * @method static Builder|Keyword query()
 * @method static Builder|Keyword whereCreatedAt($value)
 * @method static Builder|Keyword whereCurrentRankingUrl($value)
 * @method static Builder|Keyword whereFeaturedSnippetKeyword($value)
 * @method static Builder|Keyword whereFeaturedSnippetOwned($value)
 * @method static Builder|Keyword whereId($value)
 * @method static Builder|Keyword whereImportId($value)
 * @method static Builder|Keyword whereKeyword($value)
 * @method static Builder|Keyword whereSearchIntention($value)
 * @method static Builder|Keyword whereSearchVolume($value)
 * @method static Builder|Keyword whereSearchVolumeClustered($value)
 * @method static Builder|Keyword whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string $current_ranking_position
 * @property-read Collection|Event[] $events
 * @property-read int|null $events_count
 * @method static Builder|Keyword whereCurrentRankingPosition($value)
 * @property-read Collection|UrlKeyword[] $urlKeyword
 * @property-read int|null $url_keyword_count
 * @property-read Collection|UrlKeywordData[] $urlKeywordData
 * @property-read int|null $url_keyword_data_count
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

    public $fillable = [
        'keyword',
        'search_volume',
        'search_volume_clustered',
        'current_ranking_url',
        'featured_snippet_keyword',
        'featured_snippet_owned',
        'search_intention',
        'import_id',
        'current_ranking_position',
    ];

    /**
     * @return BelongsToMany
     */
    public function urls(): BelongsToMany
    {
        return $this->belongsToMany(URL::class, 'url_keyword', 'keyword_id', 'url_id');
    }

    /**
     * @return MorphMany
     */
    public function events(): MorphMany
    {
        return $this->morphMany(Event::class, 'entity')->with('user');
    }

    public function urlKeyword(): HasMany
    {
        return $this->hasMany(UrlKeyword::class);
    }

    public function urlKeywordData(): HasManyThrough
    {
        return $this->hasManyThrough(UrlKeywordData::class, UrlKeyword::class);
    }

}
