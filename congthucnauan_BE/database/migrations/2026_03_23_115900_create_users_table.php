<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable()->index('idx_phone');
            $table->text('address')->nullable();
            $table->string('avatar')->default('default-avatar.png');
            $table->string('password');
            $table->enum('role', ['user', 'manager', 'admin'])->default('user');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
