<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        {!! $fontFace !!}
        @page {
            size: A4;
            margin: 0.75cm 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .page {
            display: grid;
            grid-template-columns: repeat(3, 6.75cm);
            grid-template-rows: repeat(4, 6.75cm);
            gap: 0.3cm;
            break-after: page;
        }

        .card {
            width: 6.75cm;
            height: 6.75cm;
            padding: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-front {
            padding: 0.3cm;
            background: radial-gradient(circle at 40% 40%, #000000, #555555);
        }

        .card-front svg {
            width: 4cm;
            height: 4cm;
        }

        .card-back {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            border-width: 0.3cm;
            padding: 0.5cm;
            border-style: solid;
            position: relative;
            width: 100%;
            height: 100%;
        }

        .card-back .song-name {
            font-size: 1.2em;
            font-weight: 600;
            letter-spacing: -0.01cm;
            text-align: center;
            width: 100%;
            color: rgb(32, 35, 42);
        }

        .card-back .artist {
            font-size: 1em;
            font-weight: 500;
            font-style: italic;
            text-align: center;
            width: 100%;
            color: rgb(48, 52, 68);
        }

        .card-back .year {
            font-size: 4.5em;
            font-weight: 700;
            letter-spacing: -0.05cm;
            text-align: center;
            width: 100%;
            color: rgb(32, 35, 42);
            margin-bottom: -0.2cm;
        }

        .card-back .top {
            display: flex;
            flex-direction: column;
            gap: 0.3em;
            align-items: center;
        }
    </style>
</head>
<body>

@foreach ($chunks as $chunk)

    {{-- Front page: QR codes --}}
    <div class="page">
        @foreach ($chunk['front'] as $card)
            <div class="card card-front">
                @if (trim($card['id']))
                    {!! $card['qr_svg'] !!}
                @endif
            </div>
        @endforeach
    </div>

    {{-- Back page: song info --}}
    <div class="page">
        @foreach ($chunk['back'] as $card)
            @php $color = $card['color']; @endphp
            <div
                class="card"
                style="
                    background:
                      radial-gradient(circle at 40% 35%, rgba(255,255,255,0.25), transparent 65%),
                      radial-gradient(circle at 40% 40%,
                        color-mix(in srgb, white 25%, {{ $color }}),
                        color-mix(in srgb, white 10%, {{ $color }})
                      );
                "
            >
                <div class="card-back" style="border-color: {{ $color }};">
                    @if (trim($card['id']))
                        <div class="top">
                            <span class="song-name">{{ $card['name'] }}</span>
                            <span class="artist">{{ $card['artist'] }}</span>
                        </div>
                        <span class="year">{{ $card['release_year'] }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

@endforeach

</body>
</html>
