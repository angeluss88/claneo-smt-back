<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\UrlKeywordData
 *
 * @property int $id
 * @property int $url_keyword_id
 * @property string|null $position
 * @property string|null $clicks
 * @property string|null $impressions
 * @property string|null $ctr
 * @property Carbon|null $date
 * @method static Builder|UrlKeywordData newModelQuery()
 * @method static Builder|UrlKeywordData newQuery()
 * @method static Builder|UrlKeywordData query()
 * @method static Builder|UrlKeywordData wherePosition($value)
 * @method static Builder|UrlKeywordData whereClicks($value)
 * @method static Builder|UrlKeywordData whereId($value)
 * @method static Builder|UrlKeywordData whereImpressions($value)
 * @method static Builder|UrlKeywordData whereCtr($value)
 * @method static Builder|UrlKeywordData whereDate($value)
 * @method static Builder|UrlKeywordData whereUrlKeywordId($value)
 * @mixin Eloquent
 */
class UrlKeywordData extends Model
{
    use HasFactory;

    public $table = 'url_keyword_data';

    public $timestamps = false;

    public $fillable = [
        'url_keyword_id',
        'position',
        'clicks',
        'impressions',
        'ctr',
        'date',
    ];

    /**
     * @return BelongsTo
     */
    public function urlData(): BelongsTo
    {
        return $this->belongsTo(UrlData::class);
    }
}
