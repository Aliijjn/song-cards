<?php

namespace App\Enum;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum CurationTypeEnum: string
{
    case Personal  = 'personal';   // created by a user
    case Editorial = 'editorial';  // system / algorithm generated
    case Era       = 'era';        // decade / time-period based (90s, 2010s, …)

    public function label(): string
    {
        return match($this) {
            self::Personal  => 'Personal',
            self::Editorial => 'Editorial',
            self::Era       => 'Era',
        };
    }
}
