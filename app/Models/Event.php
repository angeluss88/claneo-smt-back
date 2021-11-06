<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Event extends Model
{
    use HasFactory;

    const CREATE_ACTION = 'create';
    const UPDATE_ACTION = 'update';

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
