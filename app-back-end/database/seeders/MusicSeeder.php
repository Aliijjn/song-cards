<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MusicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private function name_clean(string $name): string
    {
        $name = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);

        $name = strtolower($name);                 // lower-case
        $name = preg_replace('/\s+/', '_', $name); // spaces → underscores
        $name = preg_replace('/[^a-z0-9_]/', '', $name); // keep only [a-z0-9_]
        $name = preg_replace('/_+/', '_', $name);  // collapse multiple _
        return trim($name, '_');
    }

    public function run(): void
    {
        DB::transaction(function () {

            $file = File::get(database_path("seeders/data.json"));
            $data = json_decode($file, true);
            $now = Carbon::now()->format('Y-m-d H:i:s');

            $artists = $data['artists'];
            foreach ($artists as $artist) {
                DB::table('artists')->insert([
                    'name' => $artist['name'],
                    'created_at' => $now,
                ]);
            }

            $albums = $data['albums'];
            foreach ($albums as $album) {
                $artist_id = Artist::query()
                    ->where('name', $album['artist_name'])
                    ->firstOrFail()
                    ->id;

                DB::table('albums')->insert([
                    'name' => $album['name'],
                    'name_clean' => $this->name_clean($album['name']),
                    'release_date' => $album['release_date'],
                    'artist_id' => $artist_id,
                    'created_at' => $now,
                ]);
            }

            $songs = $data['songs'];
            foreach ($songs as $song) {
                $album = Album::query()
                    ->where('name', $song['album_name'])
                    ->firstOrFail();
                $album_id = $album->id;
                $artist_id = $album->artist->id;

                DB::table('songs')->insert([
                    'name' => $song['name'],
                    'duration_seconds' => $song['duration_seconds'],
                    'views_on_spotify' => $song['views_on_spotify'],
                    'album_id' => $album_id,
                    'artist_id' => $artist_id,
                    'created_at' => $now,
                ]);
            }

        });
    }
}
