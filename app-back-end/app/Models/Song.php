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
        $clutter = ['remaster', 'original', 'raw', 'mix', 'mono', 'stereo', 'medley', 'extend', 'version', 'album', 'single', 'edit'];

        return collect(preg_split('#(?=[\(\-/])#', $name))
            ->filter(fn(string $name) => !Str::contains(Str::lower($name), $clutter))
            ->join('');
    }

    public static function fromSongsRaw(Collection $songs): void
    {
        $now = now();

        $spotifySongIds = $songs->pluck('id');
        $spotifyAlbumIds = $songs->pluck('album.id');
        $spotifyArtistIds = $songs->flatMap(fn($s) => collect($s['artists'])->pluck('id'))->unique();

        // -----------------------------
        // Load existing data in bulk
        // -----------------------------
        $existingSongs = Song::whereIn('spotify_id', $spotifySongIds)->get()->keyBy('spotify_id');
        $existingAlbums = Album::whereIn('spotify_id', $spotifyAlbumIds)->get()->keyBy('spotify_id');
        $existingArtists = Artist::whereIn('spotify_id', $spotifyArtistIds)->get()->keyBy('spotify_id');

        // -----------------------------
        // Build ID maps (no queries inside loops)
        // -----------------------------
        $songIdMap = [];
        $albumIdMap = [];
        $artistIdMap = [];

        foreach ($songs as $song) {

            // SONG
            $songIdMap[$song['id']] = $existingSongs[$song['id']]->id
                ?? ($songIdMap[$song['id']] ??= Uuid::uuid7()->toString());

            // ALBUM
            $albumIdMap[$song['album']['id']] = $existingAlbums[$song['album']['id']]->id
                ?? ($albumIdMap[$song['album']['id']] ??= Uuid::uuid7()->toString());

            // ARTISTS
            foreach ($song['artists'] as $artist) {
                $artistIdMap[$artist['id']] = $existingArtists[$artist['id']]->id
                    ?? ($artistIdMap[$artist['id']] ??= Uuid::uuid7()->toString());
            }
        }

        // -----------------------------
        // Prepare bulk inserts
        // -----------------------------
        $songRows = [];
        $pivotRows = [];

        foreach ($songs as $song) {

            $songRows[] = [
                'id' => $songIdMap[$song['id']],
                'spotify_id' => $song['id'],
                'name' => static::parseName($song['name']),
                'album_id' => $albumIdMap[$song['album']['id']],
                'duration_ms' => $song['duration_ms'],
                'popularity' => $song['popularity'],
                'track_number' => $song['track_number'],
                'errors' => SongErrorEnum::fromTrack($song),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            foreach ($song['artists'] as $artist) {
                $pivotRows[] = [
                    'song_id' => $songIdMap[$song['id']],
                    'artist_id' => $artistIdMap[$artist['id']],
                ];
            }
        }

        // -----------------------------
        // Bulk insert (idempotent-safe if you use unique indexes)
        // -----------------------------
        Song::upsert($songRows, ['spotify_id'], ['name', 'album_id', 'duration_ms', 'popularity', 'track_number', 'updated_at']);

        DB::table('artist_song')->upsert(
            $pivotRows,
            ['song_id', 'artist_id']
        );
    }
}
