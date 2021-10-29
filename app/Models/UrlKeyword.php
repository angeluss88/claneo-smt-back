<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UrlKeyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UrlKeyword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UrlKeyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|UrlKeyword whereClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UrlKeyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UrlKeyword whereCtr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UrlKeyword whereCurrentRankingPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UrlKeyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UrlKeyword whereImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UrlKeyword whereKeywordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UrlKeyword whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UrlKeyword whereUrlId($value)
 * @mixin \Eloquent
 */
class UrlKeyword extends Model
{
    use HasFactory;

    public $table = 'url_keyword';
}
