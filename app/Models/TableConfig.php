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
 * @property string table_id
 * @property string $column
 * @property int $position
 * @property-read User $user
 * @method static TableConfig|Builder newModelQuery()
 * @method static TableConfig|Builder newQuery()
 * @method static TableConfig|Builder query()
 * @method static TableConfig|Builder whereTableId($value)
 * @method static TableConfig|Builder whereColumn($value)
 * @method static TableConfig|Builder wherePosition($value)
 * @method static TableConfig|Builder whereId($value)
 * @method static TableConfig|Builder whereUserId($value)
 * @mixin Eloquent
 */
class TableConfig extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'table_id',
        'column',
        'position',
    ];

    protected $table = 'user_config';

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
