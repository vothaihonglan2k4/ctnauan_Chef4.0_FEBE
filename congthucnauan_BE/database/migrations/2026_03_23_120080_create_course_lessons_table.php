<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_free')->default(false);
            $table->string('image')->default('no-image.jpg');
            $table->text('summary')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index('course_id');
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};
