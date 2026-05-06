<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cv_templates', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->string('badge', 30)->nullable();
            $table->string('badge_color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('style_config')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cv_templates', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'description',
                'badge',
                'badge_color',
                'sort_order',
                'style_config',
                'updated_at'
            ]);
        });
    }
};
