<?php

namespace App\Models;

use App\Data\CurationCombineDTO;
use App\Data\CurationCopyDTO;
use App\Tools\Classes\DefaultModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class Curation extends DefaultModel
{
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class);
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
                $maxCount,
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

        $to->songs()->sync($songs);
        $to->songEdits()->sync($songEdits);
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

    /**
     * @param Collection<string> $ids
     */
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
}
