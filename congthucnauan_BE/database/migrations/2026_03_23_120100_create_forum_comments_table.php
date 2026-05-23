<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('post_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->text('content');
            $table->timestamp('created_at')->useCurrent();

            $table->index('post_id', 'idx_post_id');
            $table->index('user_id', 'idx_user_id');
            $table->index('parent_id', 'idx_parent_id');

            $table->foreign('post_id')->references('id')->on('forum_posts')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('forum_comments')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_comments');
    }
};
