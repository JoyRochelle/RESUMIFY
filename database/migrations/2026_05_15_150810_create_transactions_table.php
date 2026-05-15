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
        Schema::create('transactions', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('user_id', 26);
            $table->string('midtrans_order_id', 100)->unique();
            $table->string('midtrans_transaction_id', 100)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 50)->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'expired'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
