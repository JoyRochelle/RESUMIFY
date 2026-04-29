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
        Schema::create('chameleon_adaptations', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('cv_id', 26);
            $table->string('target_company', 150)->nullable();
            $table->string('tone_style', 50)->nullable();
            $table->longText('adapted_content')->nullable()->comment('JSON');
            $table->text('ai_prompt_used')->nullable();
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
        Schema::dropIfExists('chameleon_adaptations');
    }
};
