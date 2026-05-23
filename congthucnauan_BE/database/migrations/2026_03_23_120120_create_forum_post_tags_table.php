<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_post_tags', function (Blueprint $table) {
            $table->unsignedInteger('post_id');
            $table->unsignedInteger('tag_id');

            $table->primary(['post_id', 'tag_id']);
            $table->index('post_id', 'idx_post_id');
            $table->index('tag_id', 'idx_tag_id');

            $table->foreign('post_id')->references('id')->on('forum_posts')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('forum_tags')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_post_tags');
    }
};
