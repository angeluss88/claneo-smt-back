<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\URL
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Keyword[] $keywords
 * @property-read int|null $keywords_count
 * @method static \Illuminate\Database\Eloquent\Builder|URL newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|URL newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|URL query()
 * @mixin \Eloquent
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

    /**
     * @return BelongsToMany
     */
    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class, 'url_keyword', 'url_id')
            ->withPivot('current_ranking_position', 'clicks', 'impressions', 'ctr');
    }

}
