<?php

namespace App\Http\Controllers;

use App\Data\CurationCombineDTO;
use App\Data\CurationCopyDTO;
use App\Data\CurationCreationDTO;
use App\Data\CurationCreationFromSongsDTO;
use App\Data\SongEditCreationDTO;
use App\Data\CurationDTO;
use App\Data\CurationUpdateDTO;
use App\Models\Curation;
use App\Models\Export;
use App\Models\Song;
use App\Models\SongEdit;
use App\Services\SpotifyApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurationController extends Controller
{
    public function all(Request $request): JsonResponse
    {
        /**
         * TODO: Spatie's data package loves RAM (reflection for each song)
         * Either fix this or don't use spatie here
         */

        ini_set('memory_limit', '512M');
        return new JsonResponse(
            CurationDTO::collect(
                Curation::orderByDesc('updated_at')
                    ->with(['songs', 'songs.album.images', 'songEdits'])
                    ->skip($request->query('start', 0))
                    ->take($request->query('length', 10))
                    ->get()
            )
        );
    }

    public function index(Curation $curation): JsonResponse
    {
        return new JsonResponse(
            CurationDTO::fromModel(
                $curation->load(['songs', 'songs.album.images', 'songEdits'])
            )
        );
    }

    public function update(Curation $curation, Request $request): JsonResponse
    {
        $updateDTO = CurationUpdateDTO::from($request->get('update'));

        $curation->update($updateDTO->toArray());

        return new JsonResponse(status: 201);
    }

    public function copy(Curation $curation, Request $request): JsonResponse
    {
        $copyDto = CurationCopyDTO::from($request->get('copy'));

        return new JsonResponse($curation->copy($copyDto)->id);
    }

    public function combine(Curation $curation, Request $request): JsonResponse
    {
        $combineDto = CurationCombineDTO::from($request->get('combine'));

        return new JsonResponse($curation->combine($combineDto)->id);
    }

    public function export(Curation $curation): JsonResponse
    {
        /**
         * The Typst plugin for QR is insanely slow :))
         *  Should probably use Gotenberg eventually
         */
        ini_set('max_execution_time', '12000');

        return new JsonResponse(
            Export::fromCuration($curation)->id
        );
    }

    public function create(Request $request, SpotifyApiService $apiService): JsonResponse
    {
        $curation = CurationCreationDTO::from($request->get('curation'));

        $curationIds = $apiService->getPlaylists($curation->playlistIds, $curation);

        return new JsonResponse($curationIds->first());
    }

    public function createFromSongs(Curation $curation, Request $request): JsonResponse
    {
        $creationDto = CurationCreationFromSongsDTO::from($request->input('creationDto'));
        $id = Curation::fromSongs($curation, $creationDto);

        return new JsonResponse($id, 201);
    }

    public function delete(Curation $curation): JsonResponse
    {
        return new JsonResponse(status: $curation->delete() ? 204 : 404);
    }

    public function deleteSong(Curation $curation, Song $song): JsonResponse
    {
        $curation->songs()->detach($song->id);
        $curation->songs()
            ->orderByPivot('order')
            ->get()
            ->map(fn($song, $i) => $curation->songs()->updateExistingPivot(
                $song->id,
                ['order' => $i]
            ));

        return new JsonResponse(status: 204);
    }

    public function deleteSongs(Curation $curation, Request $request): JsonResponse
    {
        $songIds = $request->input('songIds');

        $curation->songs()->detach($songIds);
        $curation->songs()
            ->orderByPivot('order')
            ->get()
            ->map(fn($song, $i) => $curation->songs()->updateExistingPivot(
                $song->id,
                ['order' => $i]
            ));

        return new JsonResponse(status: 204);
    }

    public function addEdit(Curation $curation, Request $request): JsonResponse
    {
        $creationDto = SongEditCreationDTO::from($request->get('songEdit'));
        $now = now();

        SongEdit::upsert($creationDto->toArray(), ['song_id']);

        DB::table('curation_song_edit')->upsert([
            'curation_id' => $curation->id,
            'song_edit_id' => SongEdit::whereSongId($creationDto->song_id)->first()->id,
            'created_at' => $now,
            'updated_at' => $now,
        ], ['curation_id', 'song_edit_id']);

        return new JsonResponse(status: 201);
    }
}
