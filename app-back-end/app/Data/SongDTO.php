<?php

namespace App\Data;

use App\Models\Song;
use Spatie\LaravelData\Data;
use Carbon\CarbonImmutable;
class SongDTO extends Data
{
    public function __construct(
        public string $name,
        public string $artist_name,
        public string $album_name,
        public string $album_name_clean,
        public int $duration_seconds,
        public int $views_on_spotify,
        public CarbonImmutable $release_date,
    ) {}

    public static function fromModel(Song $song): self
    {
        return new self(
            $song->name,
            $song->artist->name,
            $song->album->name,
            $song->album->name_clean,
            $song->duration_seconds,
            $song->views_on_spotify,
            $song->album->release_date,
        );
    }
}
