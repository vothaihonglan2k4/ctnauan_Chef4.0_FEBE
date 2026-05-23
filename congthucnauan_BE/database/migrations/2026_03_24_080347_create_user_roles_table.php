<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('branch_id')->nullable(); // nullable theo yêu cầu
            $table->timestamp('created_at')->useCurrent();

            // 1 user có thể có nhiều role theo chi nhánh
            $table->unique(['user_id', 'role_id', 'branch_id'], 'uq_user_role_branch');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')->on('roles')
                ->onDelete('cascade');

            $table->index('branch_id', 'idx_user_roles_branch');
            // Nếu sau này có bảng branches thì thêm FK branch_id sau
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
