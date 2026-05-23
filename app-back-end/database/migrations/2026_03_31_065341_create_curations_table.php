<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    const SPOTIFY_ID_LEN = 22;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('curations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('system_generated')->default(false);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('curation_song', function (Blueprint $table) {
            $table->uuid('curation_id');
            $table->char('song_id', self::SPOTIFY_ID_LEN);
            $table->integer('order')->nullable();

            $table->timestamps();

            $table->foreign('curation_id')
                ->references('id')
                ->on('curations')
                ->cascadeOnDelete();

            $table->foreign('song_id')
                ->references('id')
                ->on('songs')
                ->cascadeOnDelete();

            $table->unique(['curation_id', 'song_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curation_song');
        Schema::dropIfExists('curations');
    }
};
