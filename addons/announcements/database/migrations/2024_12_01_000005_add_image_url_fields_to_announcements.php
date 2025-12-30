<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This addon is the property of the CLIENTXCMS association.
 * Year: 2024
 */

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
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('cover_image_url')->nullable()->after('cover_image');
            $table->string('og_image_url')->nullable()->after('og_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['cover_image_url', 'og_image_url']);
        });
    }
};
