<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Project
 *
 * @method static Builder|Project newModelQuery()
 * @method static Builder|Project newQuery()
 * @method static Builder|Project query()
 * @mixin Eloquent
 * @property int $id
 * @property string $domain
 * @property int|null $client_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Project whereCreatedAt($value)
 * @method static Builder|Project whereDomain($value)
 * @method static Builder|Project whereId($value)
 * @method static Builder|Project whereUpdatedAt($value)
 * @method static Builder|Project whereUserId($value)
 * @property string|null $ga_property_id
 * @property string|null $ua_property_id
 * @property string|null $ua_view_id
 * @property-read Client|null $client
 * @property-read Collection|URL[] $urls
 * @property-read int|null $urls_count
 * @method static Builder|Project whereGaPropertyId($value)
 * @method static Builder|Project whereUaPropertyId($value)
 * @method static Builder|Project whereUaViewId($value)
 */
class Project extends Model
{
    use HasFactory;

    const GA_STRATEGY = 'ga_property';
    const UA_STRATEGY = 'ua_property';
    const NO_EXPAND_STRATEGY = 'no_expand';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'domain',
        'client_id',
        'ga_property_id',
        'ua_property_id',
        'ua_view_id',
        'strategy',
    ];

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return HasMany
     */
    public function urls(): HasMany
    {
        return $this->hasMany(URL::class);
    }
}
