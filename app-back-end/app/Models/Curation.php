<?php

namespace App\Models;

use App\Data\CurationCopyDTO;
use App\Tools\Classes\DefaultModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function copy(CurationCopyDTO $copyDto): Curation
    {
        $this->loadMissing(['songs', 'songEdits']);

        $now = now();
        $newCuration = Curation::create([
            'name' => $copyDto->name,
            'description' => $copyDto->description,
            'created_by' => $copyDto->userId
        ]);

        $newCuration->songs()->sync(
            $this->songs
                ->mapWithKeys(fn($song) => [
                    $song->id => [
                        'curation_id' => $newCuration->id,
                        'song_id' => $song->id,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]
                ])
                ->when(
                    $copyDto->maxSongCount,
                    fn($query) => $query->random($copyDto->maxSongCount)
                )
        );
        $selectedSongIds = $newCuration->songs->pluck('id');

        $newCuration->songEdits()->sync(
            $this->songEdits
                ->whereIn('song_edit_id', $selectedSongIds)
                ->mapWithKeys(fn($songEdit) => [
                    $songEdit->id => [
                        'curation_id' => $newCuration->id,
                        'song_edit_id' => $songEdit->id,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]
                ])
        );

        return $newCuration;
    }
}
