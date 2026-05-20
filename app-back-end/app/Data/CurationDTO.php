<?php

namespace App\Data;

use App\Models\Curation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CurationDTO extends Data
{
    /**
     * @param Collection<SongDTO> $songs
     */
    public function __construct(
        public string     $id,
        public string     $name,
        public ?string    $description,
        public string     $createdBy,
        public Carbon     $updatedAt,
        public Collection $songs,
    )
    {
    }

    public static function fromModel(Curation $curation): self
    {
        $songEdits = $curation->songEdits
            ->mapWithKeys(fn($songEdit) => [$songEdit->song_id => $songEdit]);

        return new self(
            $curation->id,
            $curation->name,
            $curation->description,
            $curation->system_generated ? 'System Generated' : $curation->createdBy?->name ?? 'Unknown User',
            $curation->updated_at,
            $curation->songs->map(
                fn($song) => SongDTO::fromModel($song, $songEdits[$song->id] ?? null)
            ),
        );
    }
}
