<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineLog extends Model
{
    protected $fillable = [
        'user_id',
        'medicine_name',
        'log_date',
        'scheduled_time',
        'taken_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
        'taken_at' => 'datetime',
    ];

    public static function medicines(): array
    {
        return [
            'TB'            => ['time' => '10:30', 'label' => 'TB ওষুধ'],
            'ART'           => ['time' => '20:00', 'label' => 'ART ওষুধ (TDF+3TC+DTG)'],
            'DTG'           => ['time' => '08:00', 'label' => 'Extra DTG'],
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isTaken(): bool
    {
        return $this->status === 'taken';
    }

    public function isMissed(): bool
    {
        return $this->status === 'missed';
    }
}
