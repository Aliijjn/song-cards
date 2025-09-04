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
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('genre_song', function (Blueprint $table) {
            $table->foreignId('genre_id')->constrained('genres')->cascadeOnDelete();
            $table->foreignId('song_id')->constrained('songs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_song');
        Schema::dropIfExists('genres');
    }
};
