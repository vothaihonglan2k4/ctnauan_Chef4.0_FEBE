<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('duration')->default(0);
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->unsignedInteger('classroom_id');
            $table->unsignedInteger('user_id');
            $table->string('image')->default('no-image.jpg');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->text('requirements')->nullable();
            $table->text('what_will_learn')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index('classroom_id');
            $table->index('user_id');

            $table->foreign('classroom_id')->references('id')->on('classrooms')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
