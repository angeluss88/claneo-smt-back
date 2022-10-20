<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\UrlKeyword
 *
 * @property int $id
 * @property int $url_id
 * @property string|null $ecom_conversion_rate
 * @property string|null $revenue
 * @property string|null $avg_order_value
 * @property string|null $bounce_rate
 * @property Carbon|null $date
 * @method static Builder|UrlData newModelQuery()
 * @method static Builder|UrlData newQuery()
 * @method static Builder|UrlData query()
 * @method static Builder|UrlData whereRevenue($value)
 * @method static Builder|UrlData whereStatus($value)
 * @method static Builder|UrlData whereId($value)
 * @method static Builder|UrlData whereEcomConversionRate($value)
 * @method static Builder|UrlData whereBounceRate($value)
 * @method static Builder|UrlData whereImpressions($value)
 * @method static Builder|UrlData whereKeywordId($value)
 * @method static Builder|UrlData whereUpdatedAt($value)
 * @method static Builder|UrlData whereUrlId($value)
 * @mixin Eloquent
 * @property-read URL $url
 * @method static Builder|UrlData whereAvgOrderValue($value)
 * @method static Builder|UrlData whereDate($value)
 */
class UrlData extends Model
{
    use HasFactory;

    public $table = 'url_data';

    public $timestamps = false;

    public $fillable = [
        'url_id',
        'ecom_conversion_rate',
        'revenue',
        'avg_order_value',
        'bounce_rate',
        'date',
    ];

    /**
     * @return BelongsTo
     */
    public function url(): BelongsTo
    {
        return $this->belongsTo(URL::class);
    }
}
