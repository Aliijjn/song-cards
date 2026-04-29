<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CurationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])
    ->group(function () {

        Route::get('/genres', [SongController::class, 'getGenres']);
        Route::get('/songs', [SongController::class, 'getSongs']);

        Route::get('/auth', [AuthController::class, 'index']);
        Route::get('/callback', [AuthController::class, 'callback']);

        Route::get('/playlists', [PlaylistController::class, 'index']);

        Route::prefix('curations')->group(function () {
            Route::get('/', [CurationController::class, 'all']);
            Route::get('/{curation}', [CurationController::class, 'index']);
            Route::put('/{curation}', [CurationController::class, 'update']);
            Route::post('/{curation}/copy', [CurationController::class, 'copy']);
            Route::get('/{curation}/export', [CurationController::class, 'export']);
            Route::post('/', [CurationController::class, 'create']);

            Route::delete('/{curation}', [CurationController::class, 'delete']);
            Route::delete('/{curation}/{song}', [CurationController::class, 'deleteSong']);
            
            Route::put('/{curation}/edit', [CurationController::class, 'addEdit']);
        });

        Route::prefix('export')->group(function () {
            Route::post('/data', [ExportController::class, 'fetchData']);
            Route::post('/', [ExportController::class, 'runExport']);
            Route::get('/', [ExportController::class, 'fetchExports']);
        });

        Route::get('/downloads/{uuid}', function ($uuid) {
            return Storage::disk('public')->download("$uuid.pdf");
        });

        Route::prefix('user')->group(function () {
            Route::get('/spotify-token-validity/{user}', [UserController::class, 'getSpotifyTokenValidity']);
        });

    });

Route::middleware(['web'])->get('/spotify/callback', [AuthController::class, 'callback']);
