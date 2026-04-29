<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CurationCreationDTO extends Data
{
    /**
     * @param Collection<string> $playlistIds
     */
    public function __construct(
        public string     $name,
        public ?string    $description = null,
        public int        $userId,
        public Collection $playlistIds,
    )
    {
    }
}
