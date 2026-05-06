<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CurationCombineDTO extends Data
{
    public function __construct(
        public string     $name,
        public ?string    $description = null,
        public int        $userId,
        public bool       $keepOriginal,
        public Collection $curationIds,
    )
    {
    }
}
