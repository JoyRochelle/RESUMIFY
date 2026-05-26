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
        Schema::create('cv_sections', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            $table->char('cv_id', 26);
            $table->enum('type', ['personal_info', 'work_experience', 'education', 'skills']);
            $table->string('title', 100);
            $table->longText('content')->nullable()->comment('JSON');
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
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
        Schema::dropIfExists('cv_sections');
    }
};
