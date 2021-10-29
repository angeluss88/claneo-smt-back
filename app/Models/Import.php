<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Import
 *
 * @property int $id
 * @property int $user_id
 * @property int $project_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Import query()
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereUserId($value)
 * @mixin \Eloquent
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

}
