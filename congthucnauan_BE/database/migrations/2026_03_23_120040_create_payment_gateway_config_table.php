<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateway_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gateway', 50);
            $table->string('config_key', 100);
            $table->text('config_value')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            $table->unique(['gateway', 'config_key'], 'uk_gateway_key');
            $table->index('gateway', 'idx_gateway');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_config');
    }
};
