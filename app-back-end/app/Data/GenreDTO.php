<?php

namespace App\Data;

use App\Models\Genre;
use Spatie\LaravelData\Data;

class GenreDTO extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $showcased_album,
        public string $genre_type,
    ) {}

    public static function fromModel(Genre $genre): self
    {
        return new self(
            $genre->id,
            $genre->name,
            $genre->description,
            $genre->showcased_album->name_clean.env('ALBUM_COVER_FILE_TYPE'),
            $genre->genre_type->value,
        );
    }
}
