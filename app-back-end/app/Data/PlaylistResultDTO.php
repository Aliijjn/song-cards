<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PlaylistResultDTO extends Data
{
    /**
     * @param Collection<PlaylistDTO> $playlists
     * @param bool $isLast
     */
    public function __construct(
        public Collection $playlists,
        public bool $isLast,
    ) {}

    public static function fromResponse(array $response): self
    {
        return new self(
            collect($response['items'])
                ->map(fn ($item) => PlaylistDTO::fromResponse($item))
                ->filter(fn ($playlist) => $playlist->songCount)
                ->values(),
            $response['next'] === null,
        );
    }
}
