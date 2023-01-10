<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="/favicon.png"/>

        <script type="text/javascript" src="{{ asset('js/darkmode.js') }}" defer></script>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        
        @if(config('app.env') == 'production')
            @include('partials.google')
            @include('partials.hotjar')
        @endif

        <!-- Scripts -->
        <script src="{{ mix('js/guest.js') }}" defer></script>
    </head>
    <body>
        <div id="guest-container" class="font-sans antialiased bg-waves dark:bg-waves-dark bg-cover">
            {{ $slot }}
        </div>
    </body>
</html>
