<?php

namespace App\Http\Controllers;

use App\Data\SongDTO;
use App\Models\Song;
use Illuminate\Http\JsonResponse;

class SongController extends Controller
{
    public function getSongs(): JsonResponse
    {
        return new JsonResponse(
            SongDTO::collect(Song::with(['artist', 'album'])->get())
        );
    }
}
