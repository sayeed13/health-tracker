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
        Schema::create('task_definitions', function (Blueprint $table) {
            $table->id();
            $table->enum('category', [
                'medicine',
                'prayer', 
                'exercise',
                'smoking',
                'food',
                'water',
                'review'
            ]);
            $table->string('title');
            $table->string('description')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->integer('active_from_day')->default(0); // ART শুরুর কত দিন থেকে
            $table->integer('active_until_day')->nullable(); // null = সবসময়
            $table->enum('repeat_type', ['daily', 'weekly'])->default('daily');
            $table->json('repeat_days')->nullable(); // [0,1,2,3,4,5,6]
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_definitions');
    }
};
