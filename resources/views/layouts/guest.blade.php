<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="/favicon.png"/>
        @if(config('app.env') == 'production')
            @include('partials.google')
            @include('partials.hotjar')
        @endif

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        <script src="{{ mix('js/guest.js') }}" defer></script>
    </head>
    <body>
        <div id="guest-container" class="font-sans antialiased bg-waves bg-cover">
            {{ $slot }}
        </div>
    </body>
</html>
