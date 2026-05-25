<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CurationCreationFromSongsDTO extends Data
{
    /**
     * @param Collection<string> $songIds
     */
    public function __construct(
        #[Max(100)]
        public string     $name,
        public ?string    $description = null,
        public int        $userId,
        public Collection $songIds,
    )
    {
    }
}
