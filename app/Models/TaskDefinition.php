<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use illuminate\Database\Eloquent\Relations\HasMany;

class TaskDefinition extends Model
{
    protected $fillable = [
        'category',
        'title',
        'description',
        'scheduled_time',
        'active_from_day',
        'active_until_day',
        'repeat_type',
        'repeat_days',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'repeat_days' => 'array',
        'is_active' => 'boolean',
        'active_from_day' => 'integer',
        'active_until_day' => 'integer',
    ];

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLogs::class);
    }

    // আজকের দিনে এই টাস্ক active কিনা
    public function isActiveForDay(int $daysSinceStart): bool
    {
        if (!$this->is_active) return false;
        if ($daysSinceStart < $this->active_from_day) return false;
        if ($this->active_until_day && $daysSinceStart > $this->active_until_day) return false;
        return true;
    }

    // সপ্তাহের নির্দিষ্ট দিনে আছে কিনা
    public function isScheduledForWeekday(int $weekday): bool
    {
        if ($this->repeat_type === 'daily') return true;
        return in_array($weekday, $this->repeat_days ?? []);
    }
}
