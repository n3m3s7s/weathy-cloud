<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Weathy App</title>
    <link rel="preload" as="image" href="/images/icons/loading.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @include('partials.icons')
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/places.js@1.19.0" defer></script>

    <script>
        const Weathy = Object.freeze({
          "places": {!! json_encode(config('services.places')) !!}
        })
    </script>
    <!-- Main JS -->
    <script src="{{ mix('js/manifest.js') }}" defer></script>
    <script src="{{ mix('js/vendor.js') }}" defer></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="bg-gradient-to-r from-green-400 to-blue-500 subpixel-antialiased">
    <div id="app" class="flex justify-center pt-16">
        <weather-component :default-location="{
                name: '{!! config('settings.default.name') !!}',
                lat: {!! config('settings.default.lat') !!},
                lon: {!! config('settings.default.lon') !!}
                }"></weather-component>
    </div>
</body>
</html>
