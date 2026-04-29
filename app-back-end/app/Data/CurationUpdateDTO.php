<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CurationUpdateDTO extends Data
{
    public function __construct(
        public string  $name,
        public ?string $description
    )
    {
    }
}
