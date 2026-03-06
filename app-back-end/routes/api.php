<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])
    ->group(function () {

        Route::get('/genres', [SongController::class, 'getGenres']);
        Route::get('/songs', [SongController::class, 'getSongs']);

        Route::get('/auth', [AuthController::class, 'index']);
        Route::get('/callback', [AuthController::class, 'callback']);

        Route::get('/playlists', [PlaylistController::class, 'index']);

        Route::prefix('cards')->group(function () {
            Route::post('/data', [CardController::class, 'fetchData']);
            Route::post('/create', [CardController::class, 'createCards']);
        });

        Route::get('/downloads/{uuid}', function ($uuid) {
            return Storage::disk('public')->download("$uuid.pdf");
        });

    });

Route::middleware(['web'])->get('/spotify/callback', [AuthController::class, 'callback']);
