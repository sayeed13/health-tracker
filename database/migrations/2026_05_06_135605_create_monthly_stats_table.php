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
        Schema::create('monthly_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('year_month', 7); // 2026-05
            $table->string('category', 20);
            $table->integer('total_tasks')->default(0);
            $table->integer('completed_tasks')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'year_month', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_stats');
    }
};
