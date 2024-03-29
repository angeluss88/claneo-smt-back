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
 * App\Models\Event
 *
 * @property int $id
 * @property int $user_id
 * @property string $entity_type
 * @property array $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $action
 * @property int $entity_id
 * @property array|null $oldData
 * @property-read Model|Eloquent $entity
 * @property-read User $user
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @method static Builder|Event whereAction($value)
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereData($value)
 * @method static Builder|Event whereEntityId($value)
 * @method static Builder|Event whereEntityType($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereOldData($value)
 * @method static Builder|Event whereUpdatedAt($value)
 * @method static Builder|Event whereUserId($value)
 * @mixin Eloquent
 */
class Event extends Model
{
    use HasFactory;

    const CREATE_ACTION = 'create';
    const UPDATE_ACTION = 'update';
    const DELETE_ACTION = 'delete';

    public $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'data',
        'action',
        'oldData',
    ];

    protected $casts = [
        'data' => 'array',
        'oldData' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }
}
