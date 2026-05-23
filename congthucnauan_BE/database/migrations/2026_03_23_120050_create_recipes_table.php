<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('ingredients');
            $table->text('instructions');
            $table->string('image')->default('no-image.jpg');
            $table->string('video_url')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('category_id');
            $table->index('user_id');

            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
