<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PlaylistDTO extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $ownerName,
        public ?string $imageUrl,
        public int $songCount,
    ) {}

    public static function fromResponse(array $response): self
    {
        return new self(
            $response['id'],
            $response['name'],
            $response['owner']['display_name'] ?? null,
            $response['images'][1]['url'] ?? $response['images'][0]['url'] ?? null,
            $response['tracks']['total'] ?? 0,
        );
    }
}
