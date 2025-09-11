<?php

namespace Database\Seeders;

use App\Enum\GenreTypeEnum;
use App\Models\Album;
use App\Models\Artist;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class MusicSeeder extends Seeder
{
    private const BATCH_SIZE = 256;

    /**
     * Run the database seeds.
     */

    private function wipeTables(): void
    {
        DB::table('artist_song')->delete();
        DB::table('album_artist')->delete();
        DB::table('artist_genre')->delete();

        DB::table('songs')->delete();
        DB::table('albums')->delete();
        DB::table('artists')->delete();
        DB::table('genres')->delete();
    }

    public function editArtists(Collection &$artists, mixed $artistsData): void
    {
        $artistsData = collect($artistsData)->keyBy('name');
        $artists = $artists->reject(function ($artist) use ($artistsData) {
            ray($artist['name'], $artistsData[$artist['name']]['edits']['delete'] ?? false);
            return ($artistsData[$artist['name']]['edits']['delete'] ?? false) === true;
        })->toArray();

        foreach ($artists as &$artist) {
            $edits = $artistsData[$artist['name']]['edits'] ?? [];
            foreach ($edits as $editKey => $editValue) {
                if ($editKey === 'delete') {
                    continue;
                }
                if (! isset($artist[$editKey])) {
                    ray($editKey, $editValue, $artist)->label('invalid insertion')->orange();
                    continue;
                }
                ray($artist[$editKey], $editValue, $artist)->label('insertion')->blue();
                $artist[$editKey] = $editValue;
//                ray($artist)->label('artist');
            }
        }
        ray($artists)->label('artists');

        $artists = collect($artists);
    }

    private function fetchData(): array
    {
        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'client_credentials',
            'client_id' => env('SPOTIFY_CLIENT_ID'),
            'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
        ]);
        $token = $response->json()['access_token'];

        $file = File::get(database_path("seeders/data/artists.json"));
        $artistsToQuery = json_decode($file, true)['artists'];

        $artists = collect();
        foreach ($artistsToQuery as $artist) {
            if (($artist['edits']['delete'] ?? false) === true) {
                continue;
            }
            $response = Http::withToken($token)
                ->get('https://api.spotify.com/v1/search', [
                    'q'     => $artist['name'],
                    'type'  => 'artist',
                    'limit' => 1,
                ]);
            $artists->push($response->json()['artists']['items'][0]);
        }
        $artists = $artists->keyBy('id');

        $songs = collect();
        foreach ($artists as $artist) {
            $response = Http::withToken($token)
                ->get('https://api.spotify.com/v1/artists/' . $artist['id'] . '/top-tracks');
            $songs = $songs->merge($response->json()['tracks']);
        }
        $songs = $songs->unique()->keyBy('id');

        // Check if any new artists are found in songs (collaborations)
        $newArtists = $songs->pluck('artists')
            ->flatten(1)
            ->unique('id')
            ->filter(function ($artist) use ($artists) {
                return ! $artists->contains('id', $artist['id']);
            })
            ->keyBy('id');

        $artists = $artists->merge($newArtists);
        $this->editArtists($artists, $artistsToQuery);

        $albums = $songs->pluck('album')
            ->unique()
            ->values()
            ->keyBy('id');

        $genres = $artists
            ->filter(fn ($artist) => isset($artist['genres']))
            ->pluck('genres')
            ->flatten()
            ->unique()
            ->values();

        return [
            $genres,
            $artists,
            $albums,
            $songs,
        ];
    }

    private function insertArtists(Collection $artists, CarbonImmutable $now): void
    {
        $chunks = $artists->chunk(self::BATCH_SIZE);
        foreach ($chunks as $chunk) {
            DB::table('artists')->insert(
                $chunk->map(fn ($artist) => [
                    'id'          => $artist['id'],
                    'name'        => $artist['name'],
                    'spotify_url' => $artist['external_urls']['spotify'] ?? null,
                    'created_at'  => $now,
                ])->toArray()
            );
        }
    }

    private function insertAlbums(Collection $albums, CarbonImmutable $now): void
    {
        $chunks = $albums->chunk(self::BATCH_SIZE);
        foreach ($chunks as $chunk) {
            DB::table('albums')->insert(
                $chunk->map(fn ($album) => [
                    'id'                     => $album['id'],
                    'name'                   => $album['name'],
                    'spotify_url'            => $album['external_urls']['spotify'] ?? null,
                    'album_cover_url'        => $album['images'][0]['url'],
                    'release_date'           => $album['release_date'],
                    'release_date_precision' => $album['release_date_precision'],
                    'total_tracks'           => $album['total_tracks'],
                    'created_at'             => $now,
                ])->toArray()
            );
        }

        // Album <-> Artist

        $albumArtistEntries = collect();
        foreach ($albums as $album) {
            foreach ($album['artists'] as $artist) {
                $albumArtistEntries->push([
                    'album_id'  => $album['id'],
                    'artist_id' => $artist['id'],
                ]);
            }
        }

        $chunks = $albumArtistEntries->chunk(self::BATCH_SIZE);
        foreach ($chunks as $chunk) {
            DB::table('album_artist')->insert(
                $chunk->map(fn ($albumArtist) => [
                    'album_id'   => $albumArtist['album_id'],
                    'artist_id'  => $albumArtist['artist_id'],
                    'created_at' => $now,
                ])->toArray()
            );
        }
    }

    private function insertSongs(Collection $songs, Collection $artists, CarbonImmutable $now): void
    {
        $chunks = $songs->chunk(self::BATCH_SIZE);
        foreach ($chunks as $chunk) {
            DB::table('songs')->insert(
                $chunk->map(fn ($song) => [
                    'id'           => $song['id'],
                    'name'         => $song['name'],
                    'spotify_url'  => $song['external_urls']['spotify'] ?? null,
                    'album_id'     => $song['album']['id'],
                    'duration_ms'  => $song['duration_ms'],
                    'popularity'   => $song['popularity'],
                    'track_number' => $song['track_number'],
                    'created_at'   => $now,
                ])->toArray()
            );
        }

        // Artist <-> Song

        $artistSongEntries = collect();
        foreach ($songs as $song) {
            foreach ($song['artists'] as $artist) {
                if (isset($artists[$artist['id']])) {
                    $artistSongEntries->push([
                        'artist_id' => $artist['id'],
                        'song_id'   => $song['id'],
                    ]);
                }
            }
        }

        $chunks = $artistSongEntries->chunk(self::BATCH_SIZE);
        foreach ($chunks as $chunk) {
            DB::table('artist_song')->insert(
                $chunk->map(fn ($artistSong) => [
                    'artist_id'  => $artistSong['artist_id'],
                    'song_id'    => $artistSong['song_id'],
                    'created_at' => $now,
                ])->toArray()
            );
        }
    }

    private function insertGenres(Collection $genres, Collection $artists, CarbonImmutable $now): void
    {
        $chunks = $genres->chunk(self::BATCH_SIZE);
        foreach ($chunks as $chunk) {
            DB::table('genres')->insert(
                $chunk->map(fn ($genre) => [
                    'name'       => $genre,
                    'created_at' => $now,
                ])->toArray()
            );
        }

        // Artist <-> Genre

        $genreData = DB::table('genres')
            ->whereIn('name', $genres)
            ->pluck('id', 'name');

        $artistGenreEntries = collect();
        foreach ($artists as $artist) {
            foreach ($artist['genres'] ?? [] as $genre) {
                $artistGenreEntries->push([
                    'artist_id' => $artist['id'],
                    'genre_id'  => $genreData[$genre],
                ]);
            }
        }

        $chunks = $artistGenreEntries->chunk(self::BATCH_SIZE);
        foreach ($chunks as $chunk) {
            DB::table('artist_genre')->insert(
                $chunk->map(fn ($artistGenre) => [
                    'artist_id'  => $artistGenre['artist_id'],
                    'genre_id'   => $artistGenre['genre_id'],
                    'created_at' => $now,
                ])->toArray()
            );
        }
    }

    public function run(): void
    {
        DB::transaction(function () {

            // Setup
            $this->wipeTables();
            [$genres, $artists, $albums, $songs] = $this->fetchData();
            $now = CarbonImmutable::now();

            // Print
            ray($genres)->label('genres');
            ray($artists)->label('artists');
            ray($albums)->label('albums');
            ray($songs)->label('songs');

            // To DB
            $this->insertArtists($artists, $now);
            $this->insertAlbums($albums, $now);
            $this->insertSongs($songs, $artists, $now);
            $this->insertGenres($genres, $artists, $now);

        });
    }
}
