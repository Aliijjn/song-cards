<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->away(
            'https://accounts.spotify.com/authorize?' . http_build_query([
                'response_type' => 'code',
                'client_id' => env('SPOTIFY_CLIENT_ID'),
                'redirect_uri' => 'https://127.0.0.1:8001/api/callback',
            ])
        );
    }

    public function callback(Request $request): RedirectResponse
    {
        $code = $request->input('code');

        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => 'https://127.0.0.1:8001/api/callback',
            'client_id' => env('SPOTIFY_CLIENT_ID'),
            'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
        ]);

        ray($response->json());
        $data = $response->json();

        ray($data, now(), now()->addSeconds($data['expires_in']));

        User::first()->update([
            'spotify_access_token' => $data['access_token'],
            'spotify_refresh_token' => $data['refresh_token'],
            'spotify_expires_at' => now()->addSeconds($data['expires_in']),
        ]);

        return redirect()->away(env('FRONTEND_URL'));
    }

}
