<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLog extends Model
{
    protected $fillable = [
        'user_id',
        'task_definition_id',
        'log_date',
        'is_completed',
        'completed_at',
        'notes',
        'quantity',
        'unit',
    ];

    protected $casts = [
        'log_date' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taskDefinition(): BelongsTo
    {
        return $this->belongsTo(TaskDefinition::class);
    }
}
