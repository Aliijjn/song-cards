<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CurationCollectionDTO extends Data
{
    /**
     * @param Collection<CurationSummaryDTO> $personal
     * @param Collection<CurationSummaryDTO> $editorial
     * @param Collection<CurationSummaryDTO> $era
     */
    public function __construct(
        public Collection $personal,
        public Collection $editorial,
        public Collection $era,
    ) {}
}
