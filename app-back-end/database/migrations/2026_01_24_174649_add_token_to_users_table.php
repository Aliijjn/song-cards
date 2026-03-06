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
        Schema::table('users', function (Blueprint $table) {
            $table->string('spotify_access_token')->nullable()->after('remember_token');
            $table->string('spotify_refresh_token')->nullable()->after('spotify_access_token');
            $table->timestamp('spotify_expires_at')->nullable()->after('spotify_refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('spotify_access_token');
            $table->dropColumn('spotify_refresh_token');
            $table->dropColumn('spotify_expires_at');
        });
    }
};
