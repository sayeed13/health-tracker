<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

#[Fillable(['name', 'email', 'password', 'timezone', 'art_start_date', 'tb_start_date'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'art_start_date' => 'date',
            'tb_start_date' => 'date',
        ];
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    public function smokingLogs(): HasMany
    {
        return $this->hasMany(SmokingLog::class);
    }

    public function medicineLogs(): HasMany
    {
        return $this->hasMany(MedicineLog::class);
    }

    public function monthlyStats(): HasMany
    {
        return $this->hasMany(MonthlyStat::class);
    }

    // ART শুরু থেকে কত দিন হলো
    public function daysSinceArtStart(): int
    {
        if (!$this->art_start_date) return 0;
        return $this->art_start_date->diffInDays(
            Carbon::now($this->timezone)
        );
    }

    // আজকের তারিখ Sri Lanka timezone এ
    public function todayInTimezone(): Carbon
    {
        return Carbon::now($this->timezone)->startOfDay();
    }
}
