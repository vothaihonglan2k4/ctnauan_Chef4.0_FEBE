<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->text('content');
            $table->string('image')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('recipe_id')->nullable();
            $table->integer('views')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->enum('status', ['active', 'hidden', 'deleted'])->default('active');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('user_id', 'idx_user_id');
            $table->index('recipe_id', 'idx_recipe_id');
            $table->index('status', 'idx_status');
            $table->index('created_at', 'idx_created_at');
            $table->index('is_pinned', 'idx_is_pinned');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('recipe_id')->references('id')->on('recipes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
    }
};
