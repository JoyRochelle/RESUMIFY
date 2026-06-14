<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview_messages', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('session_id', 26);
            $table->enum('role', ['assistant', 'user']);
            $table->text('content');
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('interview_sessions')->onDelete('cascade');

            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_messages');
    }
};
