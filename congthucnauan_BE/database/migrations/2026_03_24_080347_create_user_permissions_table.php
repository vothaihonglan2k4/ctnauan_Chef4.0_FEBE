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
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('permission_id');
            $table->unsignedInteger('branch_id')->nullable();
            $table->boolean('is_granted')->default(true); // true: grant, false: deny override
            $table->timestamp('created_at')->useCurrent();

            $table->unique(
                ['user_id', 'permission_id', 'branch_id'],
                'uq_user_permission_branch'
            );

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id')->on('permissions')
                ->onDelete('cascade');

            $table->index('branch_id', 'idx_user_permissions_branch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
