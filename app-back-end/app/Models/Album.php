<?php

namespace App\Models;

use App\Tools\Classes\DefaultModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class Album extends DefaultModel
{
    protected function casts(): array
    {
        return [
            'release_date' => 'immutable_date',
        ];
    }

    public function artist(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class);
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable');
    }

    public static function parseReleaseDate(string $rawDate): CarbonImmutable
    {
        ray($rawDate);
        $d = preg_split('/-/', $rawDate);
        ray($d);

        return CarbonImmutable::startOfTime()
            ->setYear((int)($d[0] ?? '1970'))
            ->setMonth((int)($d[1] ?? '1'))
            ->setDay((int)($d[2] ?? '1'));
    }

    public static function fromAlbumsRaw(Collection $albums, ?Collection $artistIds): void
    {
        $now = now();
        $albums = $albums->filter(
            fn($album) => !static::whereSpotifyId($album['id'])
                ->exists()
        );

        $albums->map(fn($album) => [
            'id' => Uuid::uuid7($now)->toString(),
            'spotify_id' => $album['id'],
            'name' => $album['name'],
            'type' => $album['type'],
            'release_date' => static::parseReleaseDate($album['release_date']),
            'release_date_precision' => $album['release_date_precision'],
            'total_tracks' => $album['total_tracks'],
            'created_at' => $now,
            'updated_at' => $now,
        ])
            ->chunk(100)
            ->map(fn($chunk) => static::insert($chunk->toArray()));

        $artistIds ??= $albums->flatMap(
            fn($album) => collect($album['artists'])->pluck('id')
        )->unique();
        $artistMap = $artistIds->mapWithKeys(fn($spotifyId) => [
            $spotifyId => Artist::whereSpotifyId($spotifyId)->first()->id,
        ]);
        ray($artistMap);

        $now = now();
        $albums->flatMap(
            fn($album) => collect($album['artists'])->map(
                fn($artist) => [
                    'album_id' => Album::whereSpotifyId($album['id'])->first()->id,
                    'artist_id' => $artistMap[$artist['id']],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            )
        )
            ->chunk(100)
            ->map(
                fn($chunk) => DB::table('album_artist')
                    ->insert($chunk->toArray())
            );
    }
}
