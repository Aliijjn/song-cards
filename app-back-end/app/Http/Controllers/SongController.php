<?php

namespace App\Http\Controllers;

use App\Data\GenreDTO;
use App\Data\SongDTO;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function getGenres(): JsonResponse
    {
        return new JsonResponse(
            GenreDTO::collect(Genre::with('showcased_album')->get())
        );
    }

    public function getSongs(Request $request): JsonResponse
    {
        $genreId = $request->input('genre_id', null);

        return new JsonResponse(
            SongDTO::collect(
                Song::with(['artist', 'album', 'genres'])
                    ->when($genreId, function ($query) use ($genreId) {
                        $query->whereHas('genres', function ($query) use ($genreId) {
                            $query->where('id', $genreId);
                        });
                    })
                    ->get()
            )
        );
    }
}
