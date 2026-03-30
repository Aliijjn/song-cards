<?php

namespace App\Data;

use App\Models\Export;
use App\Models\Genre;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ExportDTO extends Data
{
    public function __construct(
        public string $uuid,
        public int $user_id,
        public string $user_name,
        public string $name,
        public Carbon $created_at,
        public Carbon $updated_at,
    ) {
        ray('default');
    }

//    public static function fromModel(DimensionValue $model): self
    public static function fromModel(Export $model): self
    {
        ray('test');
        return new self(
            $model->uuid,
            $model->user_id,
            $model->user->name,
            $model->name,
            $model->created_at,
            $model->updated_at
        );
    }
}
