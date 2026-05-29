<?php

namespace App\Models;

use App\Data\CurationCombineDTO;
use App\Data\CurationCopyDTO;
use App\Data\CurationCreationDTO;
use App\Data\CurationCreationFromSongsDTO;
use App\Enum\CurationTypeEnum;
use App\Tools\Classes\DefaultModelUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class Curation extends DefaultModelUuid
{
    protected $casts = [
        'type' => CurationTypeEnum::class,
    ];

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

    public static function fromSongs(
        Curation                     $originalCuration,
        CurationCreationFromSongsDTO $creationDto,
    ): string
    {
        $now = now();
        $i = 0;

        $curation = static::create([
            'id' => Uuid::uuid7($now)->toString(),
            'name' => $creationDto->name,
            'description' => $creationDto->description,
            'type'       => CurationTypeEnum::Personal->value,
            'created_by' => $creationDto->userId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $songs = $originalCuration
            ->songs()
            ->orderByPivot('order')
            ->whereIn('id', $creationDto->songIds)
            ->get()
            ->map(function ($song) use (&$i, $now, $curation) {
                return [
                    'curation_id' => $curation->id,
                    'song_id' => $song->id,
                    'order' => $i++,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            });
        // todo: doesn't copy songedit

        $curation->songs()->sync($songs);

        return $curation->id;
    }

    private function copyTo(Curation $to, ?int $maxCount = null): void
    {
        $this->loadMissing(['songs', 'songEdits']);

        $i = $to->songs->count();
        ray($to, $this);
        $now = now();
        $songs = $this->songs
            ->when(
                $maxCount !== null && $maxCount > 0,
                fn($query) => $query->random($maxCount)
            )
            ->mapWithKeys(function ($song) use (&$i, $now, $to) {
                return [
                    $song->id => [
                        'curation_id' => $to->id,
                        'song_id' => $song->id,
                        'order' => $i++,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]
                ];
            });
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
        ray($to->songs, $songs);
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
        $newCuration->recalculateOrder();

        return $newCuration;
    }

    public function recalculateOrder(): void
    {
        $this->songs()
            ->orderByPivot('order')
            ->get()
            ->map(fn($song, $i) => $this->songs()->updateExistingPivot(
                $song->id,
                ['order' => $i]
            ));
    }

    public static function fromRawData(Collection $playlists, Collection $songs): Collection
    {
        $now = now();
        $values = $playlists->mapWithKeys(fn($playlist) => [
            $playlist['id'] => [
                'id' => Uuid::uuid7($now)->toString(),
                'name' => $playlist['name'],
                'description' => $playlist['description'], // todo: beware of sketchy HTML
                'type'             => CurationTypeEnum::Editorial->value,
                'created_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
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

        Curation::insert($values->toArray());
        DB::table('curation_song')->insertOrIgnore($curationSong);

        return $values->pluck('id');
    }

    public static function oneFromRawData(CurationCreationDTO $curation, Collection $songs): Collection
    {
        $now = now();
        $curationId = Uuid::uuid7($now)->toString();

        Curation::insertGetId([
            'id' => $curationId,
            'name' => $curation->name,
            'description' => $curation->description,
            'type'       => CurationTypeEnum::Personal->value,
            'created_by' => $curation->userId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $i = 0;
        $curationSong = $songs
            ->sortBy(function ($song, $key) {
                $split = explode(':', $key);

                return (int)$split[1] + (int)$split[2];
            })
            ->sortBy(fn($song, $key) => strstr($key, ':', true))
            ->map(function ($song) use (&$i, $curationId, $now) {
                return [
                    'curation_id' => $curationId,
                    'song_id' => $song['id'],
                    'order' => $i++,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->toArray();

        DB::table('curation_song')->insertOrIgnore($curationSong);

        return collect([$curationId]);
    }
}
