<?php

use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

// Explicit middleware call might be overkill
Route::middleware(['api'])
    ->group(function () {

        Route::get('/genres', [songController::class, 'getGenres']);
        Route::get('/songs', [SongController::class, 'getSongs']);

    });
