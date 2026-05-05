<?php

namespace App\Models;

use App\Data\CurationCombineDTO;
use App\Data\CurationCopyDTO;
use App\Tools\Classes\DefaultModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
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

    public function copy(CurationCopyDTO|CurationCombineDTO $copyDto): Curation
    {
        $this->loadMissing(['songs', 'songEdits']);

        $now = now();
        $newCuration = Curation::create([
            'name' => $copyDto->name,
            'description' => $copyDto->description,
            'created_by' => $copyDto->userId
        ]);
        $songs = $this->songs
            ->when(
                $copyDto?->maxSongCount ?? null,
                fn($query) => $query->random($copyDto->maxSongCount)
            )
            ->mapWithKeys(fn($song) => [
                $song->id => [
                    'curation_id' => $newCuration->id,
                    'song_id' => $song->id,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ]);
        $selectedSongIds = $songs->keys();
        ray($this->songEdits, $selectedSongIds);
        $songEdits = $this->songEdits
            ->whereIn('song_id', $selectedSongIds)
            ->mapWithKeys(fn($songEdit) => [
                $songEdit->id => [
                    'curation_id' => $newCuration->id,
                    'song_edit_id' => $songEdit->id,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ]);

        $newCuration->songs()->sync($songs);
        $newCuration->songEdits()->sync($songEdits);

        return $newCuration;
    }

    /**
     * @param Collection<string> $ids
     */
    public function combine(Collection $ids): void
    {

    }
}
