<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const BASE_62_ID_LENGTH = 22;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {

            // All the tables with actual content:

            Schema::create('genres', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('album_id', self::BASE_62_ID_LENGTH)
                    ->nullable();
                $table->foreign('album_id')
                    ->references('id')
                    ->on('albums')
                    ->nullOnDelete();
                $table->text('description')
                    ->nullable();
                $table->timestamps();
            });

            Schema::create('artists', function (Blueprint $table) {
                $table->string('id', self::BASE_62_ID_LENGTH)->primary();
                $table->string('name');
                $table->string('spotify_url');
                $table->timestamps();
            });

            Schema::create('albums', function (Blueprint $table) {
                $table->string('id', self::BASE_62_ID_LENGTH)->primary();
                $table->string('name');
                $table->string('spotify_url')->nullable();
                $table->string('album_cover_url');
                $table->dateTime('release_date');
                $table->string('release_date_precision');
                $table->integer('total_tracks');
                $table->timestamps();
            });

            Schema::create('songs', function (Blueprint $table) {
                $table->string('id', self::BASE_62_ID_LENGTH)->primary();
                $table->string('name');
                $table->string('spotify_url');
                $table->string('album_id', self::BASE_62_ID_LENGTH);
                $table->foreign('album_id')
                    ->references('id')
                    ->on('albums')
                    ->cascadeOnDelete();
                $table->integer('duration_ms');
                $table->integer('popularity');
                $table->integer('track_number');
                $table->dateTime('release_date')
                    ->nullable();
                $table->timestamps();
            });

            // Pivot tables:

            Schema::create('artist_genre', function (Blueprint $table) {
                $table->string('artist_id', self::BASE_62_ID_LENGTH);
                $table->foreign('artist_id')
                    ->references('id')
                    ->on('artists')
                    ->cascadeOnDelete();
                $table->foreignId('genre_id')
                    ->constrained()
                    ->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['artist_id', 'genre_id']);
            });

            Schema::create('album_artist', function (Blueprint $table) {
                $table->string('album_id', self::BASE_62_ID_LENGTH);
                $table->foreign('album_id')
                    ->references('id')
                    ->on('albums')
                    ->cascadeOnDelete();
                $table->string('artist_id', self::BASE_62_ID_LENGTH);
                $table->foreign('artist_id')
                    ->references('id')
                    ->on('artists')
                    ->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['album_id', 'artist_id']);
            });

            Schema::create('artist_song', function (Blueprint $table) {
                $table->string('artist_id', self::BASE_62_ID_LENGTH);
                $table->foreign('artist_id')
                    ->references('id')
                    ->on('artists')
                    ->cascadeOnDelete();
                $table->string('song_id', self::BASE_62_ID_LENGTH);
                $table->foreign('song_id')
                    ->references('id')
                    ->on('songs')
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
