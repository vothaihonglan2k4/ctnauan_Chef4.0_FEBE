<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('payment_id')->nullable();
            $table->dateTime('enrollment_date')->useCurrent();
            $table->dateTime('completion_date')->nullable();
            $table->integer('progress')->default(0);
            $table->string('status', 50)->default('active');

            $table->unique(['user_id', 'course_id'], 'user_course_unique');
            $table->index('course_id');
            $table->index('payment_id');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->foreign('payment_id')->references('id')->on('payments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
