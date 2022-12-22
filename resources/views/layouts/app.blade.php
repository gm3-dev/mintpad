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
        
        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>
    <body class="font-sans antialiased">
        <div id="app" class="main-container min-h-screen bg-primary-100 dark:bg-mintpad-500" data-page="{{ Route::currentRouteName() }}" data-user="{{ Auth::user()->id }}">
            @include('partials.navigation')
            @isset($header)
                <div class="p-6 text-left w-full mx-auto bg-gray-100">
                    <div class="px-10 w-full sm:max-w-7xl mx-auto">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <!-- Page Content -->
            <div id="app-loader" class="w-10 mx-auto mt-4 text-lg dark:text-white"><img src="/images/icon.svg" class="h-[35px] animate-bounce" /></div>
            <x-bg-overlay id="app-loader-bg" class="hidden"></x-bg-overlay>
            <main id="app-content" class="hidden">
                <div class="col-span-1 lg:col-span-2">
                    <div v-if="hasValidChain !== true" class="bg-mintpad-200 p-2 mb-4 text-center">
                        <p class="text-sm text-mintpad-700">Your wallet is not connected to the correct blockchain <x-button href="#" class="ml-4" @click.prevent="switchBlockchainTo(false)">Switch blockchain</x-button></p>
                    </div>
                </div>
                <div class="py-12">
                    {{ $slot }}
                </div>
                <x-modal-vue></x-modal-vue>
                @include('partials.messages')
            </main>
        </div>

        <!-- Scripts -->
        <script type="text/javascript" src="{{ asset('js/darkmode.js') }}" defer></script>
        <!-- <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script> -->
        <script src="https://cdn.tiny.cloud/1/6zk5wmqbfgxkjqvqyh5f1y44fqollc7y639edh5dt2295z6r/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script src="{{ mix('js/app.js') }}" defer></script>
    </body>
</html>
