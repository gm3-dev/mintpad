<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>
        <meta name="description" content="@yield('description')">
        <!-- Twitter -->
        @hasSection('sharing-image')
        <meta name="twitter:image:src" content="@yield('sharing-image')">
        @endif
        <meta name="twitter:site" content="@mintpadco">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="@yield('title')">
        <meta name="twitter:description" content="@yield('description')">
        <!-- Open Graph -->
        @hasSection('sharing-image')
        <meta property="og:image" content="@yield('sharing-image')">
        <meta property="og:image:alt" content="@yield('title')">
        @endif
        <meta property="og:site_name" content="Mintpad">
        <meta property="og:type" content="object">
        <meta property="og:title" content="@yield('title')">
        <meta property="og:url" content="{{ url()->full() }}">
        <meta property="og:description" content="@yield('description')">

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

        <!-- Scripts -->
        <script src="{{ mix('js/mint.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div class="main-container min-h-screen bg-white dark:bg-mintpad-500">
            <!-- Page Content -->
            <div id="app-loader" class="w-10 mx-auto pt-4 text-lg dark:text-white"><img src="/images/icon.svg" class="h-[35px] animate-bounce" /></div>
            <main id="app" class="hidden" data-page="{{ Route::currentRouteName() }}">
                <div class="col-span-1 lg:col-span-2">
                    <div v-if="!wallet.account" class="bg-mintpad-200 dark:bg-mintpad-800 p-2 text-center">
                        <p class="text-sm text-mintpad-700 m-0">Your wallet is not connected <x-button href="#" class="ml-4" @click.prevent="connectMetaMask">Connect MetaMask</x-button></p>
                    </div>
                    <div v-else-if="hasValidChain !== true" class="bg-mintpad-200 dark:bg-mintpad-800 p-2 text-center">
                        <p class="text-sm text-mintpad-700 m-0">Your wallet is not connected to the correct blockchain <x-button href="#" class="ml-4" @click.prevent="switchBlockchainTo(false)">Switch blockchain</x-button></p>
                    </div>
                </div>
                {{ $slot }}
                @include('partials.messages')
            </main>
        </div>
    </body>
</html>
