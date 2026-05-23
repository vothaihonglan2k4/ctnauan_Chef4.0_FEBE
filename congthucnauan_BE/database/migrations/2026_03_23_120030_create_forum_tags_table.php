<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique('unique_name');
            $table->string('slug', 50)->unique('unique_slug');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_tags');
    }
};
