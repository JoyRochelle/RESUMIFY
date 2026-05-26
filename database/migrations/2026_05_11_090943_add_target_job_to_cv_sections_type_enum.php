<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to use a raw statement to update the enum
        DB::statement("ALTER TABLE cv_sections MODIFY COLUMN type ENUM('personal_info', 'work_experience', 'education', 'skills', 'target_job') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To reverse, we'd have to remove 'target_job', but only if no rows exist with that type
        // For safety, we'll just revert the enum definition
        DB::statement("ALTER TABLE cv_sections MODIFY COLUMN type ENUM('personal_info', 'work_experience', 'education', 'skills') NOT NULL");
    }
};
