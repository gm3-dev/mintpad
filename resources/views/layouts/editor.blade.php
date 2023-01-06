<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="/favicon.png"/>
        <script type="text/javascript" src="{{ asset('js/darkmode.js') }}" defer></script>
        
        @if(config('app.env') == 'production')
            @include('partials.google')
            @include('partials.hotjar')
        @endif

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>
    <body class="font-sans antialiased">
        <div class="main-container min-h-screen bg-white dark:bg-mintpad-500">
            <!-- Page Content -->
            <div id="app-loader" class="w-10 mx-auto pt-4 text-lg dark:text-white"><img src="/images/icon.svg" class="h-[35px] animate-bounce" /></div>
            <x-bg-overlay id="app-loader-bg" class="hidden"></x-bg-overlay>
            <main id="app" class="hidden" data-page="{{ Route::currentRouteName() }}">
                {{ $slot }}
                <x-modal-vue></x-modal-vue>
                @include('partials.messages')
            </main>
        </div>

        <!-- Scripts -->
        <script src="{{ mix('js/editor.js') }}" defer></script>
    </body>
</html>
