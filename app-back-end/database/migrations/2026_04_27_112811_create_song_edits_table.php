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
        Schema::create('song_edits', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("song_id")->constrained()->cascadeOnDelete();
            $table->string("name");
            $table->string("release_date");
            $table->timestamps();

            $table->unique("song_id");
        });

        Schema::create("curation_song_edit", function (Blueprint $table) {
            $table->foreignUuid('curation_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignUuid('song_edit_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['curation_id', 'song_edit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_edits');
        Schema::dropIfExists('curation_song_edit');
    }
};
