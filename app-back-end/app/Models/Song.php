<?php

namespace App\Models;

use App\Enum\SongErrorEnum;
use App\Tools\Classes\DefaultModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class Song extends DefaultModel
{
    public $casts = [
        'errors' => 'array',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class);
    }

    public function artistsString(): string
    {
        return $this->artists->pluck('name')->join(', ');
    }

    public function spotifyUrl(): string
    {
        return "https://open.spotify.com/track/$this->spotify_id";
    }

    public function curations(): BelongsToMany
    {
        return $this->belongsToMany(Curation::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    private static function parseName(string $name): string
    {
        $clutter = ['remaster', 'original', 'raw', 'mix', 'mono', 'stereo', 'medley', 'extend', 'version', 'album', 'single', 'edit', 'feat', 'including', 'ii.', 'live at', 'with the', 'bonus track'];
        $parts = collect(preg_split('#(?=[\(\-/])#', $name));

        return $parts
            ->filter(fn(string $name) => !Str::contains(Str::lower($name), $clutter))
            ->join('') ?: $parts[0]; // in case of false positive, re-add the whole name
    }

    public static function fromRawData(Collection $songs): void
    {
        $now = now();
        $values = $songs->map(fn($song) => [
            'id' => $song['id'],
            'name' => static::parseName($song['name']),
            'album_id' => $song['album']['id'],
            'duration_ms' => $song['duration_ms'],
            'popularity' => $song['popularity'],
            'track_number' => $song['track_number'],
            'errors' => SongErrorEnum::fromTrack($song),
        ])->toArray();

        Song::upsert($values, ['id']);

        $artistSong = $songs->flatMap(fn($song) => collect($song['artists'])->map(fn($artist) => [
            'artist_id' => $artist['id'],
            'song_id' => $song['id'],
            'created_at' => $now,
            'updated_at' => $now,
        ]))->toArray();

        DB::table('artist_song')->insertOrIgnore($artistSong);
    }
}
