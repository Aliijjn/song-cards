<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class Image extends Model
{
    use HasUuids, HasTimestamps;

    protected $guarded = ['id'];

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
}
