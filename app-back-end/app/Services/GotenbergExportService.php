<?php

namespace App\Services;

use App\Data\CurationDTO;
use App\Data\SongDTO;
use App\Models\Curation;
use App\Models\Export;
use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class GotenbergExportService
{
    public static function fromCuration(Curation $curation, bool $skipErrors = false): Export
    {
        $curationDto = CurationDTO::fromModel($curation);

        $songs = $skipErrors
            ? $curationDto->songs->filter(fn(SongDTO $song) => $song->errors->isEmpty())
            : $curationDto->songs;

        $chunks = $songs
            ->map(fn(SongDTO $song) => [
                'id'           => $song->id,
                'name'         => $song->name,
                'artist'       => $song->artist_name->join(', '),
                'release_year' => $song->release_date->year,
                'color'        => ColorService::fromString($song->id),
                'url'          => $song->spotifyUrl(),
            ])
            ->chunk(Export::CARD_PAGE_COUNT)
            ->map(function ($chunk) {
                $back = $chunk->values()
                    ->pad(Export::CARD_PAGE_COUNT, Export::CARD_PAGE_PADDING)
                    ->values();

                $front = $back->chunk(Export::CARD_X_COUNT)
                    ->flatMap(fn($c) => $c->reverse())
                    ->map(fn($card) => array_merge($card, [
                        'qr_svg' => trim($card['id']) ? self::qrSvg($card['url']) : '',
                    ]));

                return ['front' => $front, 'back' => $back];
            });

        $html = view('export.cards', [
            'chunks'   => $chunks,
            'fontFace' => self::embeddedFontFace('Plus+Jakarta+Sans', 'plus_jakarta_sans', '400;500;600;700;800'),
        ])->render();

        $gotenbergUrl = rtrim(config('services.gotenberg.url', 'http://localhost:3000'), '/');
        $response = Http::attach('files[0]', $html, 'index.html')
            ->post("$gotenbergUrl/forms/chromium/convert/html");

        if ($response->failed()) {
            throw new \Exception('Gotenberg render failed: ' . $response->body());
        }

        $uuid = Uuid::uuid7();
        Storage::disk('public')->put("$uuid.pdf", $response->body());

        return Export::create([
            'id'      => $uuid,
            'user_id' => User::firstOrFail()->id,
            'name'    => $curationDto->name,
        ]);
    }

    private static function qrSvg(string $url): string
    {
        return (new SvgWriter())->write(
            new QrCode(data: $url, margin: 0)
        )->getString();
    }

    private static function embeddedFontFace(string $family, string $cacheKey, string $weights = '400'): string
    {
        if (Storage::disk('local')->exists($cacheKey)) {
            return Storage::disk('local')->get($cacheKey);
        }

        $css = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ])->get("https://fonts.googleapis.com/css2?family=$family:wght@$weights&display=swap")->body();

        $fontFace = preg_replace_callback(
            '/url\(([^)]+\.woff2)\)/',
            function ($m) {
                $data = Http::get($m[1])->body();
                return "url('data:font/woff2;base64," . base64_encode($data) . "')";
            },
            $css
        );

        Storage::disk('local')->put($cacheKey, $fontFace);

        return $fontFace;
    }
}
