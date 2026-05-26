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
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('user_id', 26);
            $table->enum('action_type', [
                'ats_analyze',
                'bullet_optimize',
                'generate_versions',
                'interview_question',
                'interview_feedback',
            ]);
            $table->unsignedInteger('tokens_used')->default(0);
            $table->decimal('cost_usd', 8, 6)->default(0);
            $table->char('resume_id', 26)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->index(['user_id', 'action_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
