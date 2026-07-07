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
        Schema::create('cv_section_snapshots', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('cv_id', 26);
            $table->longText('sections')->comment('JSON: array of {type, title, content, order}');
            $table->string('reason', 30)->nullable();
            $table->char('source_adaptation_id', 26)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('cv_id')
                  ->references('id')
                  ->on('cvs')
                  ->onDelete('cascade');

            $table->foreign('source_adaptation_id')
                  ->references('id')
                  ->on('chameleon_adaptations')
                  ->nullOnDelete();

            $table->index('cv_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_section_snapshots');
    }
};
