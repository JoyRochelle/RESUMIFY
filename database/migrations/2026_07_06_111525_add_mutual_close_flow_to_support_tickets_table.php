<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE support_tickets MODIFY status ENUM('open', 'pending', 'awaiting_closure', 'closed') DEFAULT 'open'");

        Schema::table('support_tickets', function (Blueprint $table) {
            $table->char('close_requested_by', 26)->nullable()->after('assigned_to');
            $table->timestamp('close_requested_at')->nullable()->after('close_requested_by');

            $table->foreign('close_requested_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropForeign(['close_requested_by']);
            $table->dropColumn(['close_requested_by', 'close_requested_at']);
        });

        DB::statement("ALTER TABLE support_tickets MODIFY status ENUM('open', 'pending', 'closed') DEFAULT 'open'");
    }
};
