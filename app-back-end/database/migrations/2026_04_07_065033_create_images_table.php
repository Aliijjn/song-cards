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
        Schema::create('images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('url');
            $table->integer('width');
            $table->integer('height');
            $table->timestamps();

            $table->unique('url');
        });

        Schema::create('imageables', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('image_id');
            $table->char('imageable_id', self::SPOTIFY_ID_LEN);
            $table->string('imageable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
        Schema::dropIfExists('imageables');
    }
};
