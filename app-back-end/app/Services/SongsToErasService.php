<?php

namespace App\Services;

use App\Enum\CurationTypeEnum;
use App\Models\Curation;
use App\Models\Song;
use Illuminate\Support\Collection;

class SongsToErasService
{
    public static function run(): void
    {
        $now = now();

        Curation::whereType(CurationTypeEnum::Era->value)->delete();

        Song::where('popularity', '>=', 70)
            ->where('errors', '=', '[]')
            ->with('album')
            ->get()
            ->filter(fn(Song $song) => $song->album->release_date->year >= 1950)
            ->groupBy(fn(Song $song) => (int)floor($song->album->release_date->year / 10) * 10)
            ->each(function (Collection $songs, int $decade) use ($now) {
                $curation = Curation::create([
                    'name' => "{$decade}s",
                    'type' => CurationTypeEnum::Era->value,
                    'created_by' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $pivots = $songs
                    ->sortBy(fn(Song $song) => $song->album->release_date)
                    ->values()
                    ->mapWithKeys(fn(Song $song, int $i) => [
                        $song->id => ['order' => $i, 'created_at' => $now, 'updated_at' => $now],
                    ]);

                $curation->songs()->sync($pivots);
            });
    }
}
