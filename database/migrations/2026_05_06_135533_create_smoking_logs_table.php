<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('smoking_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('log_date');
            $table->enum('smoke_slot', [
                'morning',        // সকালে উঠে
                'after_breakfast',// নাস্তার পর
                'after_lunch',    // দুপুরের পর
                'evening',        // সন্ধ্যায়
                'after_office',   // অফিস শেষে
                'after_dinner',   // রাতের খাবারের পর
                'late_night'      // রাতে ঘুম না আসলে
            ]);
            $table->enum('status', ['smoked', 'skipped', 'pending'])->default('pending');
            $table->timestamps();

            $table->unique(['user_id', 'log_date', 'smoke_slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smoking_logs');
    }
};
