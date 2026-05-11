<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmokingLog extends Model
{
    protected $fillable = [
        'user_id',
        'log_date',
        'smoke_slot',
        'status',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    // সব স্লটের বাংলা নাম
    public static function slots(): array
    {
        return [
            'morning'          => 'সকালে উঠে',
            'after_breakfast'  => 'নাস্তার পর',
            'after_lunch'      => 'দুপুরের খাবারের পর',
            'evening'          => 'সন্ধ্যার ব্রেকে',
            'after_office'     => 'অফিস শেষে',
            'after_dinner'     => 'রাতের খাবারের পর',
            'late_night'       => 'রাতে দেরিতে',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
