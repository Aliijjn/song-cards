<?php

namespace App\Data;

use App\Enum\CurationTypeEnum;
use App\Models\Curation;
use App\Models\Image;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CurationSummaryDTO extends Data
{
    public function __construct(
        public string           $id,
        public string           $name,
        public ?string          $description,
        public string           $createdBy,
        public CurationTypeEnum $type,
        public Carbon           $updatedAt,
        public int              $songCount,
        public ?string          $coverUrl,
    )
    {
    }

    public static function fromModel(Curation $curation): self
    {
        $firstSong = $curation->songs->first();
        $coverUrl = $firstSong
            ? Image::getSmallestSquare(collect($firstSong->album->images), 500)?->url
            : null;

        return new self(
            $curation->id,
            $curation->name,
            $curation->description,
            $curation->type === CurationTypeEnum::Personal
                ? ($curation->createdBy?->name ?? 'Unknown User')
                : $curation->type->label(),
            $curation->type,
            $curation->updated_at,
            $curation->songs_count,
            $coverUrl,
        );
    }
}
