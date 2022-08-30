<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\URL
 *
 * @property-read Collection|Keyword[] $keywords
 * @property-read int|null $keywords_count
 * @method static Builder|URL newModelQuery()
 * @method static Builder|URL newQuery()
 * @method static Builder|URL query()
 * @mixin Eloquent
 * @property int $id
 * @property string $url
 * @property int|null $project_id
 * @property string $status
 * @property string|null $page_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $main_category
 * @property string|null $sub_category
 * @property string|null $sub_category2
 * @property string|null $sub_category3
 * @property string|null $sub_category4
 * @property string|null $sub_category5
 * @property int|null $import_id
 * @property-read Collection|Event[] $events
 * @property-read int|null $events_count
 * @property-read Project|null $project
 * @method static Builder|URL whereAvgOrderValue($value)
 * @method static Builder|URL whereCreatedAt($value)
 * @method static Builder|URL whereId($value)
 * @method static Builder|URL whereImportId($value)
 * @method static Builder|URL whereMainCategory($value)
 * @method static Builder|URL wherePageType($value)
 * @method static Builder|URL whereProjectId($value)
 * @method static Builder|URL whereSubCategory($value)
 * @method static Builder|URL whereSubCategory2($value)
 * @method static Builder|URL whereSubCategory3($value)
 * @method static Builder|URL whereSubCategory4($value)
 * @method static Builder|URL whereSubCategory5($value)
 * @method static Builder|URL whereUpdatedAt($value)
 * @method static Builder|URL whereUrl($value)
 * @property-read Collection|URL[] $urls
 * @property-read Collection|UrlData[] $urlData
 * @property-read Collection|UrlKeywordData[] $urlKeywordData
 */
class URL extends Model
{
    use HasFactory;

    const URL_KEY = 'Soll-URL';
    const PAGE_TYPE = 'Seitentyp';
    const MAIN_CATEGORY = 'Hauptkategorie';
    const SUB_CAT_1 = 'Unterkategorie 1. Ebene';
    const SUB_CAT_2 = 'Unterkategorie 2. Ebene';
    const SUB_CAT_3 = 'Unterkategorie 3. Ebene';
    const SUB_CAT_4 = 'Unterkategorie 4. Ebene';
    const SUB_CAT_5 = 'Unterkategorie 5. Ebene';
    const URL__STATUS = 'URL Status';

    const URL_KEY_EN = 'URL';
    const PAGE_TYPE_EN = 'Page type';
    const MAIN_CATEGORY_EN = 'Main Category';
    const SUB_CAT_1_EN = 'Subcategory_1';
    const SUB_CAT_2_EN = 'Subcategory_2';
    const SUB_CAT_3_EN = 'Subcategory_3';
    const SUB_CAT_4_EN = 'Subcategory_4';
    const SUB_CAT_5_EN = 'Subcategory_5';
    const URL__STATUS_EN = 'URL status';

    protected $table = 'urls';

    public $fillable = [
        'url',
        'project_id',
        'status',
        'main_category',
        'sub_category',
        'sub_category2',
        'sub_category3',
        'sub_category4',
        'sub_category5',
        'page_type',
        'import_id',
    ];

    /**
     * @return BelongsToMany
     */
    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class, 'url_keyword', 'url_id');
    }

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return MorphMany
     */
    public function events(): MorphMany
    {
        return $this->morphMany(Event::class, 'entity')->with('user');
    }

    /**
     * @return HasMany
     */
    public function urlData(): HasMany
    {
        return $this->hasMany(UrlData::class, 'url_id');
    }

    /**
     * @return HasManyThrough
     */
    public function urlKeywordData(): HasManyThrough
    {
        return $this->hasManyThrough(UrlKeywordData::class, UrlKeyword::class, 'url_id', 'url_keyword_id');
    }

    public function seoEvents(): MorphMany
    {
        return $this->morphMany(SeoEvent::class, 'entity');
    }

}
