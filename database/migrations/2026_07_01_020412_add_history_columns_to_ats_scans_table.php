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
        Schema::table('ats_scans', function (Blueprint $table) {
            // Who ran the scan (nullable for backwards compat with old rows)
            $table->char('user_id', 26)->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Target job meta (auto-filled from resume or manually entered)
            $table->string('job_title')->nullable()->after('cv_id');
            $table->string('job_company')->nullable()->after('job_title');

            // Full AI result JSON so we can re-render without re-calling the AI
            $table->longText('result_json')->nullable()->after('suggestions');

            // Make cv_id nullable so we can store scans without a CV
            $table->char('cv_id', 26)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ats_scans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'job_title', 'job_company', 'result_json']);
            $table->char('cv_id', 26)->nullable(false)->change();
        });
    }
};
