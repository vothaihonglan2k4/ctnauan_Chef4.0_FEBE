<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_lesson_completion', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('lesson_id');
            $table->timestamp('completed_at')->useCurrent();

            $table->unique(['user_id', 'lesson_id'], 'unique_completion');
            $table->index('user_id');
            $table->index('course_id');
            $table->index('lesson_id');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->foreign('lesson_id')->references('id')->on('course_lessons')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lesson_completion');
    }
};
