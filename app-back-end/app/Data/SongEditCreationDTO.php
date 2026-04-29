<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SongEditCreationDTO extends Data
{
    public function __construct(
        public string $song_id,
        public string $name,
        public string $release_date,
    )
    {
    }
}
