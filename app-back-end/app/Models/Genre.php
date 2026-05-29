<?php

namespace App\Models;

use App\Tools\Classes\DefaultModelUuid;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class Genre extends DefaultModelUuid
{
    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class);
    }

    /**
     * @param Collection<array> $artists Raw Spotify artist objects, each with a 'genres' string array
     */
    public static function fromRawData(Collection $artists): void
    {
        $now = now();

        $genreNames = $artists
            ->flatMap(fn($artist) => $artist['genres'] ?? [])
            ->unique()
            ->values();

        if ($genreNames->isEmpty()) {
            return;
        }

        $existing = static::whereIn('name', $genreNames)->get()->keyBy('name');

        $missing = $genreNames->filter(fn($name) => !$existing->has($name));

        if ($missing->isNotEmpty()) {
            static::insert($missing->map(fn($name) => [
                'id'         => Uuid::uuid7($now)->toString(),
                'name'       => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray());

            $existing = static::whereIn('name', $genreNames)->get()->keyBy('name');
        }

        $pivots = $artists->flatMap(fn($artist) => collect($artist['genres'] ?? [])
            ->filter(fn($name) => $existing->has($name))
            ->map(fn($name) => [
                'artist_id'  => $artist['id'],
                'genre_id'   => $existing[$name]->id,
                'created_at' => $now,
                'updated_at' => $now,
            ])
        )->toArray();

        DB::table('artist_genre')->insertOrIgnore($pivots);
    }
}
