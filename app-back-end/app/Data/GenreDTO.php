<?php

namespace App\Data;

use App\Models\Genre;
use Spatie\LaravelData\Data;

class GenreDTO extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}

    public static function fromModel(Genre $genre): self
    {
        return new self(
            $genre->id,
            $genre->name,
        );
    }
}
