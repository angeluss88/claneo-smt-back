<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\UrlKeyword
 *
 * @property int $id
 * @property int $url_id
 * @property int $keyword_id
 * @property int $current_ranking_position
 * @property string|null $clicks
 * @property string|null $impressions
 * @property string|null $ctr
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UrlKeyword newModelQuery()
 * @method static Builder|UrlKeyword newQuery()
 * @method static Builder|UrlKeyword query()
 * @method static Builder|UrlKeyword whereClicks($value)
 * @method static Builder|UrlKeyword whereCreatedAt($value)
 * @method static Builder|UrlKeyword whereCtr($value)
 * @method static Builder|UrlKeyword whereCurrentRankingPosition($value)
 * @method static Builder|UrlKeyword whereId($value)
 * @method static Builder|UrlKeyword whereImpressions($value)
 * @method static Builder|UrlKeyword whereKeywordId($value)
 * @method static Builder|UrlKeyword whereUpdatedAt($value)
 * @method static Builder|UrlKeyword whereUrlId($value)
 * @mixin Eloquent
 * @property-read Collection|UrlKeywordData[] $urlKeywordData
 * @property-read int|null $url_keyword_data_count
 */
class UrlKeyword extends Model
{
    use HasFactory;

    public $table = 'url_keyword';

    public function urlKeywordData(): HasMany
    {
        return $this->hasMany(UrlKeywordData::class);
    }
}
