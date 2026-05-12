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
        Schema::table('cvs', function (Blueprint $table) {
            $table->string('job_target', 100)->nullable();
            $table->string('company_target', 100)->nullable();
            $table->integer('ats_score')->default(0);
            $table->enum('status', ['draft', 'completed'])->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cvs', function (Blueprint $table) {
            $table->dropColumn(['job_target', 'company_target', 'ats_score', 'status']);
        });
    }
};
