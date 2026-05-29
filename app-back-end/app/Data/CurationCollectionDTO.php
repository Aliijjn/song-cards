<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CurationCollectionDTO extends Data
{
    /**
     * @param Collection<CurationDTO> $personal
     * @param Collection<CurationDTO> $editorial
     * @param Collection<CurationDTO> $era
     */
    public function __construct(
        public Collection $personal,
        public Collection $editorial,
        public Collection $era,
    ) {}
}
