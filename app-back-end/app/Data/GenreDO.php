<?php

namespace App\Data;

use App\Models\Genre;
use Spatie\LaravelData\Data;

class GenreDO extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
    ) {}

    public static function fromModel(Genre $genre): self
    {
        return new self(
            $genre->id,
            $genre->name,
            $genre->description,
        );
    }

}
