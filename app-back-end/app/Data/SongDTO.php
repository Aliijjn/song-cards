<?php

namespace App\Data;

use App\Models\Song;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Carbon\CarbonImmutable;
class SongDTO extends Data
{
    public function __construct(
        public string $name,
        public Collection $artist_name,
        public string $album_name,
        public string $album_cover_url,
        public int $duration_seconds,
        public CarbonImmutable $release_date,
    ) {}

    public static function fromModel(Song $song): self
    {
        return new self(
            $song->name,
            $song->artists?->pluck("name") ?: collect('Unknown Artist'),
            $song->album->name,
            $song->album->album_cover_url,
            $song->duration_ms / 1000,
            $song->release_date ?? $song->album->release_date,
        );
    }
}
