<?php

namespace App\Models;

use App\Tools\Classes\DefaultModel;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

class Artist extends DefaultModel
{
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Album::class);
    }

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class);
    }

    public static function fromRawData(Collection $artists): void
    {
        $now = now();
        $values = $artists->map(fn($artist) => [
            'id' => $artist['id'],
            'name' => $artist['name'],
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        self::upsert($values, 'id');
    }
}
