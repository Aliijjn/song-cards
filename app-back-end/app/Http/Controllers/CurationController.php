<?php

namespace App\Http\Controllers;

use App\Data\CurationCombineDTO;
use App\Data\CurationCopyDTO;
use App\Data\CurationCreationDTO;
use App\Data\SongEditCreationDTO;
use App\Data\CurationDTO;
use App\Data\CurationUpdateDTO;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Curation;
use App\Models\Export;
use App\Models\Song;
use App\Models\SongEdit;
use App\Models\User;
use App\Services\SpotifyApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class CurationController extends Controller
{
    public function all(Request $request): JsonResponse
    {
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
        ray($curation);
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
        $newCuration = $curation->copy($copyDto);

        return new JsonResponse($newCuration->id);
    }

//    public function combine(Curation $curation, Request $request): JsonResponse
//    {
//        $combineDto = CurationCombineDTO::from($request->get('combine'));
//        $newCuration = $combineDto->keepOriginal ? $curation : $curation->copy($combineDto);
//
//
//    }

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
        $curationId = null;

        DB::transaction(function () use ($request, $apiService, &$curationId) {

            $now = now();

            $curation = CurationCreationDTO::from($request->get('curation'));

            ray($curation);

            // todo: change to http pool
            $songs = collect($curation->playlistIds)
                ->flatMap(fn($playlistId) => collect(Http::withToken(User::first()->spotify_access_token)
                    ->get("https://api.spotify.com/v1/playlists/$playlistId/tracks")
                    ->json()['items'])
                )
                ->pluck('track')
                ->unique('id');

            $albums = $apiService->getAlbums($songs->pluck('album.id')
                ->unique());

            $artists = $apiService->getArtists(
                $albums->flatMap(fn($album) => collect($album['artists'])->pluck('id'))
                    ->unique()
            );

            $genres = $artists->flatMap(fn($artist) => $artist['genres'])
                ->unique();

            function getImages(Collection $assets): Collection
            {
                return $assets->flatMap(
                    fn($asset) => collect($asset['images'])->map(
                        fn($image) => [
                            'asset_id' => $asset['id'],
                            'asset_type' => $asset['type'],
                            ...$image,
                        ]
                    )
                );
            }

            $images = collect([
                getImages($albums),
                getImages($artists),
            ])
                ->flatten(1)
                ->unique('url');

            ray($songs, $artists, $albums, $genres, $images);

            Artist::fromArtistsRaw($artists);

            Album::fromAlbumsRaw($albums, $artists->pluck('id'));

            $imagesMap = $images->map(fn($image) => [
                ...$image,
                'id' => Uuid::uuid7(now())->toString(),
                'created_at' => $now,
                'updated_at' => $now,
                match ($image['asset_type']) {
                    'artist' => Artist::whereSpotifyId($image['asset_id'])->first()->id,
                    'album' => Album::whereSpotifyId($image['asset_id'])->first()->id,
                }
            ]);
            $imagesMap->map(fn($image) => [
                'id' => $image['id'],
                'url' => $image['url'],
                'width' => $image['width'],
                'height' => $image['height'],
                'created_at' => $image['created_at'],
                'updated_at' => $image['updated_at'],
            ])
                ->chunk(100)
                ->each(fn($chunk) => DB::table('images')->insert(ray()->pass($chunk)->toArray()));

            $imagesMap->map(fn($imageable) => [
                'image_id' => $imageable['id'],
                'imageable_id' => $imageable['asset_id'],
                'imageable_type' => $imageable['asset_type'],
                'created_at' => $imageable['created_at'],
                'updated_at' => $imageable['created_at'],
            ])
                ->chunk(100)
                ->each(fn($chunk) => DB::table('imageables')->insert($chunk->toArray()));

            Song::fromSongsRaw($songs);

            $curationId = Curation::create([
                'name' => $curation->name,
                'description' => $curation->description,
                'created_by' => $curation->userId,
            ])->id;

            $songs->map(fn($song) => [
                'curation_id' => $curationId,
                'song_id' => Song::whereSpotifyId($song['id'])->first()->id,
                'created_at' => $now,
                'updated_at' => $now,
            ])
                ->chunk(100)
                ->map(fn($chunk) => DB::table('curation_song')->insert($chunk->toArray()));
        });

        return new JsonResponse($curationId);
    }

    public function delete(Curation $curation): JsonResponse
    {
        return new JsonResponse(status: $curation->delete() ? 204 : 404);
    }

    public function deleteSong(Curation $curation, Song $song): JsonResponse
    {
        return new JsonResponse(status: $curation->songs()->detach($song->id) ? 204 : 404);
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
