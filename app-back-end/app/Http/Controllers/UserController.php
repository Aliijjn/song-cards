<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function getSpotifyTokenValidity(User $user): JsonResponse
    {
        ray($user, $user->spotify_expires_at);
        return new JsonResponse($user->spotify_expires_at);
    }
}
