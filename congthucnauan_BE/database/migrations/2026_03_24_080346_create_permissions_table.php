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
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 100)->unique(); // vd: recipe.view, user.manage
            $table->string('module', 50);          // vd: recipe, user, report
            $table->string('action', 50);          // vd: view, create, update, delete
            $table->string('display_name', 150)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['module', 'action'], 'idx_permissions_module_action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
