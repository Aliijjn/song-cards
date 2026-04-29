<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

class Artist extends Model
{
    use HasUuids, HasTimestamps;

    protected $guarded = ['id'];
    protected $keyType = 'string';
    public $incrementing = false;

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

    public static function fromArtistsRaw(Collection $artists): void
    {
        $now = now();
        $artists->filter(
            fn($artist) => !static::whereSpotifyId($artist['id'])
                ->exists()
        )
            ->map(fn($artist) => [
                'id' => Uuid::uuid7($now)->toString(),
                'spotify_id' => $artist['id'],
                'name' => $artist['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->chunk(100)
            ->map(fn($chunk) => static::insert($chunk->toArray()));
    }
}
