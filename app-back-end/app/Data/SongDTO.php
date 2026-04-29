<?php

namespace App\Data;

use App\Enum\SongErrorEnum;
use App\Models\Image;
use App\Models\Song;
use App\Models\SongEdit;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Carbon\CarbonImmutable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SongDTO extends Data
{
    /**
     * @param Collection<string> $artist_name
     * @param Collection<SongErrorEnum> $errors
     */
    public function __construct(
        public string          $id,
        public string          $spotifyId,
        public string          $name,
        public Collection      $artist_name,
        public string          $album_name,
        public ?string         $albumCoverUrl,
        public int             $duration_seconds,
        public CarbonImmutable $release_date,
        public Collection      $errors,
    )
    {
    }

    public static function fromModel(Song $song, ?SongEdit $songEdit): self
    {
        return new self(
            $song->id,
            $song->spotify_id,
            $songEdit?->name ?? $song->name,
            $song->artists?->pluck("name") ?: collect('Unknown Artist'),
            $song->album->name,
            Image::getSmallestSquare(collect($song->album->images))?->url,
            $song->duration_ms / 1000,
            $songEdit?->release_date ?? $song->album->release_date,
            collect($song->errors)
        );
    }

    public function spotifyUrl(): string
    {
        return "https://open.spotify.com/track/$this->spotifyId";
    }
}
