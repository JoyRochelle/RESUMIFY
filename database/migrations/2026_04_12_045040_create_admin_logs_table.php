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
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('admin_id', 26)->comment('References users.id');
            $table->string('action', 100);
            $table->string('target_type', 50)->nullable();
            $table->char('target_id', 26)->nullable();
            $table->timestamp('created_at')->useCurrent();
 
            $table->foreign('admin_id')
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
        Schema::dropIfExists('admin_logs');
    }
};
