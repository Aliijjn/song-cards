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
            GenreDTO::collect(
                Genre::whereNotNull('album_id')
                    ->with(['album', 'artists.songs'])
                    ->get()
            )
        );
    }

    public function getSongs(Request $request): JsonResponse
    {
        $genreId = $request->input('genre_id');
        $difficulty = $request->input('difficulty', 75);

        ray(Song::get()->first()->artists)->label('test');

        $result = SongDTO::collect(
                Song::where('popularity', '>=', $difficulty)
                    ->with(['artists', 'album'])
                    ->when($genreId !== null, function ($query) use ($genreId) {
                        $query->whereHas('artists.genres', function ($subQuery) use ($genreId) {
                            $subQuery->where('genres.id', $genreId);
                        });
                    })
                    ->get()
            );
//        ray($request, $result);
        return new JsonResponse($result
        );
    }
}
