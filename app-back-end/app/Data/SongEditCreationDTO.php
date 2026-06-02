<?php

namespace App\Data;

use App\Enum\SongErrorEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SongEditCreationDTO extends Data
{
    /**
     * @param Collection<SongErrorEnum>|null $dismissed_errors
     */
    public function __construct(
        public string      $song_id,
        public string      $name,
        public string      $release_date,
        public ?Collection $dismissed_errors,
    )
    {
    }
}
