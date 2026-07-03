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
        Schema::create('ai_credit_reservations', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('user_id', 26);
            $table->unsignedInteger('credits');
            $table->unsignedInteger('usage_before');
            $table->unsignedInteger('usage_after');
            $table->unsignedInteger('quota_limit');
            $table->string('context', 100)->nullable();
            $table->enum('status', ['reserved', 'bypassed', 'denied']);
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->index(['user_id', 'status']);
            $table->index('refunded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_credit_reservations');
    }
};
