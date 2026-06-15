<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview_feedback', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('session_id', 26)->unique();
            $table->json('question_scores');
            $table->json('missing_keywords');
            $table->unsignedTinyInteger('overall_score');
            $table->enum('readiness_badge', ['ready', 'almost_ready', 'needs_practice']);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('session_id')
                  ->references('id')
                  ->on('interview_sessions')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_feedback');
    }
};
