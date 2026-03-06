<?php

namespace App\Data;

use App\Models\Genre;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Data;

class GenreDTO extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public int $song_count,
        public string $showcased_album,
    ) {}

    public static function fromModel(Genre $genre): self
    {
        return new self(
            $genre->id,
            ucwords($genre->name),
            $genre->description,
            $genre->artists
                ->flatMap(fn ($artist) => $artist->songs)
                ->unique('id')
                ->count(),
            $genre->album?->album_cover_url ?? 'Album cover URL not found',
        );
    }
}
