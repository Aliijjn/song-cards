<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Services\SpotifyApiService;
use Illuminate\Database\Seeder;

class CurationSeeder extends Seeder
{
    const PLAYLIST_IDS = [
        '1DTzz7Nh2rJBnyFbjsH1Mh'
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $apiService = new SpotifyApiService();

        $playlists = $apiService->getPlaylists(collect(self::PLAYLIST_IDS));

        ray($playlists);
    }
}
