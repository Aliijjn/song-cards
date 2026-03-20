<?php

namespace App\Http\Controllers;

use App\Data\ExportDO;
use App\Data\SongCardDTO;
use App\Enum\SongErrorEnum;
use App\Models\Export;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

ini_set('max_execution_time', '12000');
class ExportController extends Controller
{
    const CARD_X_COUNT = 3;
    const CARD_PAGE_COUNT = self::CARD_X_COUNT * 4;


    public function fetchData(Request $request): JsonResponse
    {
        [$validCards, $invalidCards] = collect($request->get("playlist_ids"))
            ->flatMap(fn ($playlistId) =>
                Http::withToken(User::first()->spotify_access_token)
                    ->get("https://api.spotify.com/v1/playlists/$playlistId/tracks")
                    ->json()['items']
            )->map(fn ($playlistObject) =>
                SongCardDTO::fromTrackObject($playlistObject['track'])
            )->unique('id')
            ->partition(fn (SongCardDTO $songCard) => SongErrorEnum::isOk($songCard->errors))
            ->map(fn (Collection $group) => $group->values());

        return new JsonResponse([
            'valid' => $validCards,
            'invalid' => $invalidCards,
        ]);
    }

    public function runExport(Request $request): JsonResponse
    {
        $request->validate([
            'card_data' => 'required|array',
        ]);

        $cardData = collect($request->get('card_data'))->chunk(self::CARD_PAGE_COUNT)
            ->map(function ($chunk) {
                $back = $chunk->values()
                    ->pad(self::CARD_PAGE_COUNT, [
                        "id" => " ",
                        "name" => " ",
                        "artist" => " ",
                        "release_year" => 0,
                        "url" => "https://open.spotify.com/track/4PTG3Z6ehGkBFwjybzWkR8",
                        "color" => "#C48ABF",
                        "errors" => 0
                    ])->values();

                $front = $back->chunk(self::CARD_X_COUNT)
                    ->flatMap(fn ($chunk) => $chunk->reverse());

                return ['front' => $front, 'back' => $back];
            });

        ray($cardData);

        Storage::disk('local')->put('data.json', json_encode($cardData->values(), JSON_PRETTY_PRINT));

        $uuid = Uuid::uuid7();
        $result = Process::run([
            'typst',
            'compile',
            '--root', base_path(),
            app_path('Services/JsonToCard.typ'),
            storage_path("app/public/$uuid.pdf"),
        ]);

        if ($result->exitCode()) {
            return new JsonResponse(
                'Job failed with error code: '.$result->exitCode().': '.$result->errorOutput(),
                500
            );
        }

        Export::create([
            'uuid' => $uuid,
            'user_id' => User::firstOrFail()->id,
            'name' => 'test',
        ]);

        return new JsonResponse([
            'preview' => Storage::url("public/$uuid.pdf"),
            'download' => "https://localhost:8001/api/downloads/$uuid",
        ]);
    }

    public function fetchExports(Request $request): JsonResponse
    {
        $user = User::whereId($request->get("user_id"))->firstOrFail();
        $start = (int) $request->get("start", 0);
        $length = (int) $request->get("length", 10);

        return new JsonResponse(
            ExportDO::collect(
                Export::where('user_id', '=', $user->id)
                    ->orderByDesc("uuid")
                    ->skip($start)
                    ->take($length)
                    ->get()
            )
        );
    }
}
