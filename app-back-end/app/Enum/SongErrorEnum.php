<?php

namespace App\Enum;

use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum SongErrorEnum: int
{
    case NonStandardAlbumType = 1;
    case PotentialLiveSong = 2;

    /**
     * @return Collection<self>
     */
    public static function fromTrack(array $track): Collection
    {
        $result = collect();

        if (!in_array($track['album']['album_type'] ?? null, ['album', 'ep'])) {
            // ray($track['album'])->label('invalid album type')->orange();
            $result->push(self::NonStandardAlbumType->value);
        }
        if (str_contains(strtolower($track['name']), 'live') || str_contains(strtolower($track['album']['name']), 'live')) {
            // ray($track)->label('potential live album')->orange();
            $result->push(self::PotentialLiveSong->value);
        }

        return $result;
    }

    public static function isOk(Collection $errors): bool
    {
        return $errors->isEmpty();
    }
}
