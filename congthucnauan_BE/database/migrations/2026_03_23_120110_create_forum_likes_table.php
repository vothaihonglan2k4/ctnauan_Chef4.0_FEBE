<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('post_id');
            $table->unsignedInteger('user_id');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['post_id', 'user_id'], 'unique_like');
            $table->index('post_id', 'idx_post_id');
            $table->index('user_id', 'idx_user_id');

            $table->foreign('post_id')->references('id')->on('forum_posts')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_likes');
    }
};
