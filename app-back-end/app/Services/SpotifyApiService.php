<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Curation;
use App\Models\Image;
use App\Models\Song;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SpotifyApiService
{
    private string $token;

    public function __construct()
    {
        $response = Http::asForm()
            ->retry(3, 1000)
            ->timeout(3)
            ->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'client_credentials',
                'client_id' => env('SPOTIFY_CLIENT_ID'),
                'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
            ]);

        if ($response->failed()) {
            throw new \Exception('failed to create token: ' . $response->body());
        }

        $this->token = $response->json()['access_token'];
    }

    /**
     * https://developer.spotify.com/documentation/web-api/reference/get-an-artist
     */
    public function getArtists(Collection $ids): Collection
    {
        return collect(
            Http::pool(function ($pool) use ($ids) {
                $ids->unique()->each(function ($id) use ($pool) {
                    $pool->withToken($this->token)
                        ->get("https://api.spotify.com/v1/artists/$id");
                });
            })
        )
            ->filter(fn(Response $response) => $response->successful())
            ->map(fn(Response $response) => $response->json())
            ->unique(fn($artist) => $artist['id']);
    }

    /**
     * https://developer.spotify.com/documentation/web-api/reference/get-an-album
     */
    public function getAlbums(Collection $ids): Collection
    {
        return collect(
            Http::pool(function ($pool) use ($ids) {
                $ids->unique()->each(function ($id) use ($pool) {
                    $pool->withToken($this->token)
                        ->get("https://api.spotify.com/v1/albums/$id");
                });
            })
        )
            ->filter(fn(Response $response) => $response->successful())
            ->map(fn(Response $response) => $response->json())
            ->unique(fn($album) => $album['id']);
    }

    public function getPlaylists(Collection $ids): Collection
    {
        ini_set('memory_limit', '-1');

        $playlists = collect(
            Http::pool(function ($pool) use ($ids) {
                $ids->unique()->each(function ($id) use ($pool) {
                    $pool->withToken($this->token)
                        ->retry(3, 1000)
                        ->timeout(3)
                        ->get("https://api.spotify.com/v1/playlists/$id");
                });
            })
        )
            ->filter(fn(Response|HttpClientException $response) => $response instanceof Response && $response->successful())
            ->map(fn(Response $response) => $response->json());

        ray($playlists);

        $requests = $playlists->flatMap(function ($playlist) {
            $tracks = $playlist['tracks'];

            $requestBase = str_replace('offset=100&limit=100', '', $tracks['next']);
            $requests = collect();
            for ($i = 100; $i < $tracks['total']; $i += 100) {
                $requests["{$playlist['id']}:$i"] = "{$requestBase}offset={$i}&limit=100";
            }
            return $requests;
        });
        $responses = collect(Http::pool(fn($pool) => $requests->mapWithKeys(fn($url, $key) => [
            $key => $pool->as($key)
                ->withToken($this->token)
                ->retry(3, 1000)
                ->get($url)
        ])))
            ->filter(fn(Response|HttpClientException $response) => $response instanceof Response && $response->successful())
            ->mapWithKeys(fn(Response $response, $key) => [$key => $response->json()])
            ->merge($playlists->mapWithKeys(fn($playlist) => ["{$playlist['id']}:0" => $playlist['tracks']]));

        ray($responses);

        $songs = $responses
            ->flatMap(fn($response, $key) => collect($response['items'])
                ->mapWithKeys(fn($item, $i) => ["$key:$i" => $item['track']])
            )
            ->filter(fn($song) => $song['id']);
        $albums = $songs
            ->pluck('album')
            ->unique('id')
            ->filter(fn($album) => $album['id']);
        $artists = $albums
            ->flatMap(fn($album) => $album['artists'])
            ->concat($songs->flatMap(fn($song) => $song['artists']))
            ->unique('id')
            ->filter(fn($artist) => $artist['id']);
        $images = $albums->flatMap(fn($album) => collect($album['images'])->map(fn($image) => [
            ...$image,
            'imageable_id' => $album['id'],
            'imageable_type' => Album::class,
        ]));

        Artist::fromRawData($artists);
        Album::fromRawData($albums);
        Image::fromRawData($images);
        Song::fromRawData($songs);
        Curation::fromRawData($playlists, $songs);

        return collect();
    }
}
