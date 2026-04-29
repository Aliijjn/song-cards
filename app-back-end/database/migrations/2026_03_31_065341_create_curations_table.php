<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('curations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('curation_song', function (Blueprint $table) {
            $table->foreignUuid('curation_id')
                ->constrained('curations')
                ->cascadeOnDelete();
            $table->foreignUuid('song_id')
                ->constrained('songs')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curations');
        Schema::dropIfExists('curation_song');
    }
};
