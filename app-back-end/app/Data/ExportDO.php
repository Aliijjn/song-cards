<?php

namespace App\Data;

use App\Models\Genre;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ExportDO extends Data
{
    public function __construct(
        public string $uuid,
        public int $user_id,
        public string $name,
        public CarbonImmutable $created_at,
        public CarbonImmutable $updated_at,
    ) {}
}
