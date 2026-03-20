<?php

namespace App\Data;

use App\Enum\SongErrorEnum;
use App\Models\Song;
use App\Services\ColorService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;
use Carbon\CarbonImmutable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SongCardDTO extends Data
{
    const MAX_NAME_LENGTH = 35;
    const MAX_ARTIST_LENGTH = 42;

    /**
     * @param Collection<SongErrorEnum> $errors
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $artist,
        public int $release_year,
        public string $url,
        public string $color,
        public Collection $errors,
        public string $imageUrl,
    ) {}

    private static function parseName(string $name): string
    {
        $clutter = ['remaster', 'original', 'raw', 'mix', 'mono', 'stereo', 'medley', 'extend', 'version', 'album', 'single', 'edit'];

        $name = collect(preg_split('#(?=[\(\-/])#', $name))
            ->filter(fn (string $name) => ! Str::contains(Str::lower($name), $clutter))
            ->join('');

        return $name;
        /*
         * strlen($name) > self::MAX_NAME_LENGTH
            ? substr($name, 0, self::MAX_NAME_LENGTH - 2).'...'
            : $name;
         */
    }

    /**
     * @param array<string> $artist
     * @return string
     */
    private static function parseArtist(array $artists): string
    {
        $artists = collect($artists)
            ->pluck('name')
            ->implode(', ');

        return $artists;
        /*
         * strlen($artists) > self::MAX_ARTIST_LENGTH
            ? Str::substr($artists, 0, self::MAX_ARTIST_LENGTH - 2).'...'
            : $artists;
         */
    }

    public static function fromTrackObject(array $track): self
    {
        return new self(
            $track['id'],
            static::parseName($track['name']),
            static::parseArtist($track['artists']),
            CarbonImmutable::parse($track['album']['release_date'])->year,
            $track['external_urls']['spotify'],
            ColorService::fromString($track['id']),
            SongErrorEnum::fromTrack($track),
            $track['album']['images'][1]['url'] ?? $track['album']['images'][0]['url'] ?? null,
        );
    }
}
