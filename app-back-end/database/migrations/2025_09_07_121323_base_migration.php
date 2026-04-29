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
        DB::transaction(function () {

            // All the tables with actual content:

            Schema::create('genres', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->timestamps();
            });

            Schema::create('artists', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('spotify_id')->unique();
                $table->string('name');
                $table->timestamps();

                $table->index('spotify_id');
            });

            Schema::create('albums', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('spotify_id')->unique();
                $table->string('name');
                $table->string('type');
                $table->string('release_date'); // cannot be a datetime because of inconsistent formatting
                $table->string('release_date_precision');
                $table->integer('total_tracks');
                $table->timestamps();

                $table->index('spotify_id');
            });

            Schema::create('songs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('spotify_id')->unique();
                $table->string('name');
                $table->foreignUuid('album_id')
                    ->constrained()
                    ->cascadeOnDelete();
                $table->integer('duration_ms');
                $table->integer('popularity');
                $table->integer('track_number');
                $table->jsonb('errors');
                $table->timestamps();

                $table->index('spotify_id');
            });

            // Pivot tables:

            Schema::create('artist_genre', function (Blueprint $table) {
                $table->foreignUuid('artist_id')
                    ->constrained()
                    ->cascadeOnDelete();
                $table->foreignUuid('genre_id')
                    ->constrained()
                    ->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['artist_id', 'genre_id']);
            });

            Schema::create('album_artist', function (Blueprint $table) {
                $table->foreignUuid('album_id')
                    ->constrained()
                    ->cascadeOnDelete();
                $table->foreignUuid('artist_id')
                    ->constrained()
                    ->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['album_id', 'artist_id']);
            });

            Schema::create('artist_song', function (Blueprint $table) {
                $table->foreignUuid('artist_id')
                    ->constrained()
                    ->cascadeOnDelete();
                $table->foreignUuid('song_id')
                    ->constrained()
                    ->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['artist_id', 'song_id']);
            });

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {

            Schema::dropIfExists('artist_song');
            Schema::dropIfExists('album_artist');
            Schema::dropIfExists('artist_genre');

            Schema::dropIfExists('songs');
            Schema::dropIfExists('albums');
            Schema::dropIfExists('artists');
            Schema::dropIfExists('genres');

        });
    }
};
