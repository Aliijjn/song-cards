<?php

namespace App\Models;

use App\Data\CurationCombineDTO;
use App\Data\CurationCopyDTO;
use App\Tools\Classes\DefaultModelUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class Curation extends DefaultModelUuid
{
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class)
            ->withPivot('order')
            ->orderByPivot('order');
    }

    public function songEdits(): BelongsToMany
    {
        return $this->belongsToMany(SongEdit::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    private function copyTo(Curation $to, ?int $maxCount = null): void
    {
        $this->loadMissing(['songs', 'songEdits']);

        $now = now();
        $songs = $this->songs
            ->when(
                $maxCount !== null && $maxCount > 0,
                fn($query) => $query->random($maxCount)
            )
            ->mapWithKeys(fn($song) => [
                $song->id => [
                    'curation_id' => $to->id,
                    'song_id' => $song->id,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ]);
        $selectedSongIds = $songs->keys();
        $songEdits = $this->songEdits
            ->whereIn('song_id', $selectedSongIds)
            ->mapWithKeys(fn($songEdit) => [
                $songEdit->id => [
                    'curation_id' => $to->id,
                    'song_edit_id' => $songEdit->id,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ]);

        $to->songs()->syncWithoutDetaching($songs);
        $to->songEdits()->syncWithoutDetaching($songEdits);
    }

    public function copy(CurationCopyDTO|CurationCombineDTO $copyDto): self
    {
        $newCuration = null;

        DB::transaction(function () use ($copyDto, &$newCuration) {
            $newCuration = Curation::create([
                'name' => $copyDto->name,
                'description' => $copyDto->description,
                'created_by' => $copyDto->userId
            ]);

            $this->copyTo($newCuration, $copyDto->maxSongCount ?? null);
        });

        return $newCuration;
    }

    public function combine(CurationCombineDTO $combineDto): self
    {
        $newCuration = null;

        DB::transaction(function () use ($combineDto, &$newCuration) {
            $newCuration = $combineDto->keepOriginal ? $this->copy($combineDto) : $this;

            Curation::whereIn('id', $combineDto->curationIds)
                ->get()
                ->each(fn($curation) => $curation->copyTo($newCuration));
        });

        return $newCuration;
    }

    public static function fromRawData(Collection $playlists, Collection $songs): void
    {
        $now = now();
        $values = $playlists->mapWithKeys(fn($playlist) => [
            $playlist['id'] => [
                'id' => Uuid::uuid7($now)->toString(),
                'name' => $playlist['name'],
                'description' => $playlist['description'], // todo: beware of sketchy HTML
                'system_generated' => true,
                'created_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ])->toArray();
        ray($songs);
        $curationSong = $songs->map(function ($song, $key) use ($values, $now) {
            $split = explode(':', $key);

            return [
                'curation_id' => $values[$split[0]]['id'],
                'song_id' => $song['id'],
                'order' => (int)$split[1] + (int)$split[2],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })
            ->toArray();
        ray($curationSong);

        Curation::insert($values);
        DB::table('curation_song')->insertOrIgnore($curationSong);
    }
}
