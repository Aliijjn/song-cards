<?php

namespace App\Data;

use App\Models\Song;
use Illuminate\Support\Facades\Storage;
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
        ray(Storage::disk('public')->exists($song->album->name_clean.'.png'), $song->album->name_clean.'.png');
        return new self(
            $song->name,
            $song->artist->name,
            $song->album->name,
            Storage::disk('public')->exists($song->album->name_clean.'.png')
                ? $song->album->name_clean.'.png'
                : 'default.png',
            $song->duration_seconds,
            $song->views_on_spotify,
            $song->album->release_date,
        );
    }
}
