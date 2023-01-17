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
    </head>
    <body class="font-sans antialiased">
        <div id="app" class="main-container min-h-screen bg-primary-100 dark:bg-mintpad-500">
            <!-- Admin bar -->
            <div class="w-full bg-primary-600 text-center text-sm">
                <div class="max-w-7xl mx-auto py-1 px-4 sm:px-6">
                    <p class="text-white dark:text-white mb-0">You are logged in as admin</p>
                </div>
            </div>

            @include('admin.partials.navigation')
            @isset($header)
                <div class="p-6 text-left w-full mx-auto bg-gray-100">
                    <div class="px-10 w-full sm:max-w-7xl mx-auto">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <!-- Page Content -->
            <div id="app-loader" class="w-10 mx-auto pt-4 text-lg dark:text-white"><img src="/images/icon.svg" class="h-[35px] animate-bounce" /></div>
            <main id="app-content" class="hidden" data-page="{{ Route::currentRouteName() }}">
                @include('partials.messages')
                <div class="py-12">
                    <div class="max-w-7xl mx-auto px-6">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>

        <!-- Scripts -->
        <script src="{{ mix('js/admin.js') }}" defer></script>
    </body>
</html>
