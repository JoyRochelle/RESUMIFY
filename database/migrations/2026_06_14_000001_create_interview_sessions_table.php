<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview_sessions', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('user_id', 26);
            $table->char('resume_id', 26);
            $table->string('job_target');
            $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('resume_id')->references('id')->on('cvs')->onDelete('cascade');

            $table->index('user_id');
            $table->index('resume_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_sessions');
    }
};
