<?php

namespace App\Data;

use App\Models\Export;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ExportDTO extends Data
{
    public function __construct(
        public string $id,
        public int $user_id,
        public string $user_name,
        public string $name,
        public Carbon $created_at,
        public Carbon $updated_at,
    ) {}

    public static function fromModel(Export $model): self
    {
        return new self(
            $model->id,
            $model->user_id,
            $model->user->name,
            $model->name,
            $model->created_at,
            $model->updated_at
        );
    }
}
