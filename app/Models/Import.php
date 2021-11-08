<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Import
 *
 * @property int $id
 * @property int $user_id
 * @property int $project_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Import newModelQuery()
 * @method static Builder|Import newQuery()
 * @method static Builder|Import query()
 * @method static Builder|Import whereCreatedAt($value)
 * @method static Builder|Import whereId($value)
 * @method static Builder|Import whereProjectId($value)
 * @method static Builder|Import whereUpdatedAt($value)
 * @method static Builder|Import whereUserId($value)
 * @mixin Eloquent
 */
class Import extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETE = 'complete';
    const TRANSLATES = [
        'ja' => 'yes',
        'nein' => 'no',
        'transaktional' => 'transactional',
        'Nicht in Top 100' => ' Not in Top 100',
        'NEU' => 'NEW',
    ];

    public $fillable = [
        'user_id', 'project_id',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

}
