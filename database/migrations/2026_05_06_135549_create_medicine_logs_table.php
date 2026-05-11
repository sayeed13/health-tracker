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
        Schema::create('medicine_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('medicine_name', ['TB', 'ART', 'Cotrimoxazole']);
            $table->date('log_date');
            $table->time('scheduled_time');
            $table->timestamp('taken_at')->nullable();
            $table->enum('status', ['taken', 'missed', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'medicine_name', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_logs');
    }
};
