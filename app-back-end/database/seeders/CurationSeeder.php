<?php

namespace Database\Seeders;

use App\Services\SongsToErasService;
use App\Services\SpotifyApiService;
use Illuminate\Database\Seeder;

class CurationSeeder extends Seeder
{
    const PLAYLIST_IDS = [
        '6PorhgXeGPsCWNkfBo21nc', // Personal Top 100
        '1DTzz7Nh2rJBnyFbjsH1Mh', // Radio 2 Top 2000
        '2T5yN80KfNFxeFAwDyqhjo', // RYM Top 1000 <- unique constraint
        '7bZ3CxF3XIzjF636T8nF7R', // Spotify 1950-2026
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $apiService = new SpotifyApiService();

        $playlists = $apiService->getPlaylists(collect(self::PLAYLIST_IDS));

        ray($playlists);

        SongsToErasService::run();
    }
}
