<?php

namespace App\Models;

use App\Data\CurationDTO;
use App\Data\SongDTO;
use App\Services\ColorService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class Export extends Model
{
    use HasUuids, HasTimestamps;

    const CARD_X_COUNT = 3;
    const CARD_PAGE_COUNT = self::CARD_X_COUNT * 4;
    const CARD_PAGE_PADDING = [
        "id" => " ",
        "name" => " ",
        "artist" => " ",
        "release_year" => 0,
        "url" => "https://open.spotify.com/track/4PTG3Z6ehGkBFwjybzWkR8",
        "color" => "#C48ABF",
        "errors" => 0
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function fromCuration(Curation $curation): self
    {
        $curationDto = CurationDTO::fromModel($curation);
        $cardData = $curationDto->songs
            ->map(fn(SongDTO $song) => [
                'id' => $song->id,
                'name' => $song->name,
                'artist' => $song->artist_name->join(', '),
                'release_year' => $song->release_date->year,
                'color' => ColorService::fromString($song->id),
                'url' => $song->spotifyUrl(),
            ])
            ->chunk(self::CARD_PAGE_COUNT)
            ->map(function ($chunk) {
                $back = $chunk->values()
                    ->pad(self::CARD_PAGE_COUNT, self::CARD_PAGE_PADDING)->values();

                $front = $back->chunk(self::CARD_X_COUNT)
                    ->flatMap(fn($chunk) => $chunk->reverse());

                return ['front' => $front, 'back' => $back];
            });

        ray($cardData);

        Storage::disk('local')->put('data.json', json_encode($cardData->values(), JSON_PRETTY_PRINT));

        $uuid = Uuid::uuid7();
        $result = Process::run([
            'typst',
            'compile',
            '--root',
            base_path(),
            app_path('Services/JsonToCard.typ'),
            storage_path("app/public/$uuid.pdf"),
        ]);

        if ($result->failed()) {
            throw new \Exception(
                'Job failed with error code: ' . $result->exitCode() . ': ' . $result->errorOutput(),
            );
        }

        return self::create([
            'id' => $uuid,
            'user_id' => User::firstOrFail()->id,
            'name' => 'test',
        ]);
    }
}
