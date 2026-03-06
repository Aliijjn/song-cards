<?php

namespace App\Http\Controllers;

use App\Data\PlaylistResultDTO;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlaylistController extends Controller
{
    public function index(request $request): JsonResponse
    {
        $response = Http::withToken(User::first()->spotify_access_token)
            ->get('https://api.spotify.com/v1/me/playlists');

        $json = $response->json();
        ray($json);

        if (! $response->successful()) {
            return new JsonResponse(
                'Spotify error: '.$json['error']['message'] ?? 'Unknown',
                $json['error']['status'] ?? 500
            );
        }

        return new JsonResponse(
            PlaylistResultDTO::fromResponse($json)
        );
    }
}
