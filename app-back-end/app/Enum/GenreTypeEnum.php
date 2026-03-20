<?php

namespace App\Enum;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum GenreTypeEnum: string
{
    case GENRE = 'genre';
    case DECADE = 'decade';
}
