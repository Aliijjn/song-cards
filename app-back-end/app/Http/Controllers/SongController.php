<?php

namespace App\Http\Controllers;

use App\Data\GenreDO;
use App\Data\SongDTO;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Http\JsonResponse;

class SongController extends Controller
{
    public function getGenres(): JsonResponse
    {
        return new JsonResponse(
            GenreDO::collect(Genre::all())
        );
    }

    public function getSongs(): JsonResponse
    {
        return new JsonResponse(
            SongDTO::collect(Song::with(['artist', 'album'])->get())
        );
    }
}
