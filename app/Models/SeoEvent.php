<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\SeoEvent
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $entity_type
 * @property int $entity_id
 * @property array $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $entity
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereTitle($value)
 * @method static Builder|Event whereDescription($value)
 * @method static Builder|Event whereDate($value)
 * @method static Builder|Event whereEntityId($value)
 * @method static Builder|Event whereEntityType($value)
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereUpdatedAt($value)
 * @mixin Eloquent
 */
class SeoEvent extends Model
{
    use HasFactory;

    const URL_TYPE = 'url';
    const PROJECT_TYPE = 'project';
    const DATE_FORMAT = 'Y-m-d';

    public $fillable = [
        'title',
        'description',
        'entity_type',
        'entity_id',
        'date',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'date',
    ];

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }
}
