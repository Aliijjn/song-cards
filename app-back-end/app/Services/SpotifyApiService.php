<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SpotifyApiService
{
    private string $token;

    public function __construct()
    {
        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'client_credentials',
            'client_id' => env('SPOTIFY_CLIENT_ID'),
            'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
        ]);
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
}
