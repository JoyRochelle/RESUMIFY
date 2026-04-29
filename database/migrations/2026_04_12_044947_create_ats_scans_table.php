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
        Schema::create('ats_scans', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('cv_id', 26);
            $table->longText('job_description');
            $table->tinyInteger('score')->unsigned()->nullable();
            $table->json('matched_keywords')->nullable();
            $table->json('suggestions')->nullable();
            $table->timestamp('created_at')->useCurrent();
 
            $table->foreign('cv_id')
                  ->references('id')
                  ->on('cvs')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ats_scans');
    }
};
