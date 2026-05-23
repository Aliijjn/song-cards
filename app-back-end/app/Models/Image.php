<?php

namespace App\Models;

use App\Tools\Classes\DefaultModelUuid;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class Image extends DefaultModelUuid
{
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function albums(): MorphToMany
    {
        return $this->morphedByMany(Album::class, 'imageable');
    }

    public function artists(): MorphToMany
    {
        return $this->morphedByMany(Artist::class, 'imageable');
    }

    public function isSquareish(): bool
    {
        $SQUARE_MARGIN = 0.05;

        if (!$this->width || !$this->height) {
            return false;
        }

        return abs($this->height / $this->width - 1) < $SQUARE_MARGIN;
    }

    /**
     * @param Collection<static> $images
     */
    public static function getSmallestSquare(Collection $images, int $minSize = 300): ?static
    {
        return $images
            ->filter(fn($image) => $image->width >= $minSize && $image->isSquareish())
            ->reduce(function (?self $smallest, self $image) {
                if ($smallest === null) {
                    return $image;
                }
                return $smallest->width < $image->width ? $smallest : $image;
            });
    }

    public static function fromRawData(Collection $images): void
    {
        $now = now();
        $images = $images->map(fn($image) => [
            ...$image,
            'id' => Uuid::uuid7($now)->toString(),
        ]);
        $values = $images->map(
            fn($image) => [
                'id' => $image['id'],
                'url' => $image['url'],
                'width' => $image['width'],
                'height' => $image['height'],
            ]
        )->toArray();

        self::upsert($values, 'url');

        $imageables = $images->map(fn($image) => [
            'image_id' => $image['id'],
            'imageable_id' => $image['imageable_id'],
            'imageable_type' => $image['imageable_type'],
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        DB::table('imageables')->insertOrIgnore($imageables);
    }
}
