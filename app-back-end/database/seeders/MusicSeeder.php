<?php

namespace Database\Seeders;

use App\Enum\GenreTypeEnum;
use App\Models\Album;
use App\Models\Artist;
use Carbon\CarbonImmutable;
use http\Exception\InvalidArgumentException;
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
            DB::table('genre_song')->delete();
            DB::table('genres')->delete();
            DB::table('albums')->delete();
            DB::table('artists')->delete();
            DB::table('songs')->delete();

            $file = File::get(database_path("seeders/data.json"));
            $data = json_decode($file, true);
            $now = Carbon::now()->format('Y-m-d H:i:s');

            $artists = $data['artists'];
            foreach ($artists as $artist) {
                try {
                    DB::table('artists')->insert([
                        'name' => $artist['name'],
                        'created_at' => $now,
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception(
                        "Invalid data for artist:\n".json_encode($artist)."\nError:\n".$e->getMessage()
                    );
                }
            }

            $albums = $data['albums'];
            foreach ($albums as $album) {
                try {
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
                } catch (\Exception $e) {
                    throw new \Exception(
                        "Invalid data for album:\n".json_encode($album)."\nError:\n".$e->getMessage()
                    );
                }
            }

            $genres = $data['genres'];
            $genreTable = [];
            foreach ($genres as $genre) {
                try {
                    $showcasedAlbumId = Album::query()
                        ->where('name', $genre['showcased_album'])
                        ->firstOrFail()
                        ->id;

                    $genreTable[$genre['name']] = DB::table('genres')->insertGetId([
                        'name' => $genre['name'],
                        'description' => $genre['description'],
                        'showcased_album_id' => $showcasedAlbumId,
                        'genre_type' => GenreTypeEnum::GENRE,
                        'created_at' => $now,
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception(
                        "Invalid data for genre:\n".json_encode($genre)."\nError:\n".$e->getMessage()
                    );
                }
            }

            $decades = $data['decades'];
            foreach ($decades as $decade) {
                try {
                    $showcasedAlbumId = Album::query()
                        ->where('name', $decade['showcased_album'])
                        ->firstOrFail()
                        ->id;

                    $genreTable[$decade['decade']] = DB::table('genres')->insertGetId([
                        'name' => $decade['decade'].'s',
                        'description' => $decade['description'],
                        'showcased_album_id' => $showcasedAlbumId,
                        'genre_type' => GenreTypeEnum::DECADE,
                        'created_at' => $now,
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception(
                        "Invalid data for decade:\n".json_encode($decade)."\nError:\n".$e->getMessage()
                    );
                }
            }

            $songs = $data['songs'];
            foreach ($songs as $song) {
                try {
                    $album = Album::query()
                        ->where('name', $song['album_name'])
                        ->firstOrFail();
                    $album_id = $album->id;
                    $artist_id = $album->artist->id;

                    $songId = DB::table('songs')->insertGetId([
                        'name' => $song['name'],
                        'duration_seconds' => $song['duration_seconds'],
                        'views_on_spotify' => $song['views_on_spotify'],
                        'album_id' => $album_id,
                        'artist_id' => $artist_id,
                        'created_at' => $now,
                    ]);

                    $rawYear = CarbonImmutable::parse($album->release_date)->year;
                    ray($rawYear);
                    $decade = intval(($rawYear % 100) / 10) * 10;
                    ray($decade);
                    DB::table('genre_song')->insert([
                        'genre_id' => $genreTable[$decade],
                        'song_id' => $songId,
                    ]);

                    foreach ($song['genres'] as $genre) {
                        DB::table('genre_song')->insert([
                            'genre_id' => $genreTable[$genre],
                            'song_id' => $songId,
                        ]);
                    }
                } catch (\Exception $e) {
                    throw new \Exception(
                        "Invalid data for song:\n\t".json_encode($song)."\n\tError:\n\t".$e->getMessage()
                    );
                }
            }
        });
    }
}
