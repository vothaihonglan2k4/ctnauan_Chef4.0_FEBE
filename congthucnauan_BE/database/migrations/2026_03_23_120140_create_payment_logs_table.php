<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payment_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('action', 100);
            $table->string('gateway', 50)->nullable();
            $table->text('request_data')->nullable();
            $table->text('response_data')->nullable();
            $table->string('status', 50)->nullable();
            $table->text('message')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('payment_id', 'idx_payment_id');
            $table->index('transaction_id', 'idx_transaction_id');
            $table->index('gateway', 'idx_gateway');
            $table->index('created_at', 'idx_created_at');

            $table->foreign('payment_id', 'fk_payment_logs_payment_id')->references('id')->on('payments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
