<?php

namespace App\Services;

use App\Models\Curation;
use App\Models\Song;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class SpotifyApiService
{
    private string $token;

    public function __construct()
    {
        $response = Http::asForm()
            ->retry(3, 1000)
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
        $now = now();
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

            if ($tracks['total'] <= 100) {
                return [];
            }

            $requestBase = str_replace('offset=100&limit=100', '', $tracks['next']);
            ray($requestBase);
            $requests = collect();
            for ($i = 0; $i < $tracks['total']; $i += 100) {
                $requests["{$playlist['id']}:$i"] = "{$requestBase}offset={$i}&limit=100";
            }
            return $requests;
        });
        ray($requests);

        $responses = collect(Http::pool(fn($pool) => $requests->mapWithKeys(fn($url, $key) => [
            $key => $pool->as($key)
                ->withToken($this->token)
                ->retry(3, 1000)
                ->timeout(3)
                ->get($url)
        ])))
            ->filter(fn(Response|HttpClientException $response) => $response instanceof Response && $response->successful())
            ->mapWithKeys(fn(Response $response, $key) => [$key => $response->json()]);

        ray($responses, $responses->first()['items'][0]['track'] ?? null);

        $songs = $responses->flatMap(
            fn($response, $key) => collect($response['items'])
                ->mapWithKeys(fn($item, $i) => ["$key:$i" => $item['track']])
        );

        ray($songs);
        $songMap = Song::addBatch($songs);

        $curations = $playlists->mapWithKeys(fn($playlist) => [
            $playlist['id'] => [
                'id' => Uuid::uuid7($now)->toString(),
                'name' => $playlist['name'],
                'description' => $playlist['description'], // todo: beware of sketchy HTML
                'system_generated' => true,
                'created_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

        ray($songMap);

        Curation::insert($curations->toArray());

        $songs->groupBy(fn($song, $key) => strstr($key, ':', true))
            ->each(function (Collection $songs, $playlistId) use ($curations, $songMap) {
                $curation = Curation::whereId($curations[$playlistId]['id'])->firstOrFail();
                $curation->songs()->syncWithoutDetaching($songs->map(fn($song) => $songMap[$song['id']]['uuid']));
            });

        return collect($curations);
    }
}
