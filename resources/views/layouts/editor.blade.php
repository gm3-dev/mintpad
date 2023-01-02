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
    </head>
    <body class="font-sans antialiased">
        <div class="main-container min-h-screen bg-primary-100">
            <!-- Page Content -->
            <div id="app-loader" class="w-10 mx-auto pt-4 text-lg dark:text-white"><img src="/images/icon.svg" class="h-[35px] animate-bounce" /></div>
            <x-bg-overlay id="app-loader-bg" class="hidden"></x-bg-overlay>
            <main id="app" class="hidden" data-page="{{ Route::currentRouteName() }}" v-bind:style="style">
                <div class="pt-16">
                    <div class="max-w-5xl mx-auto px-6">
                        {{ $slot }}
                    </div>
                </div>
                <x-modal-vue></x-modal-vue>
                @include('partials.messages')

                <div class="p-2 w-full bg-primary-600 text-white">
                    <div class="max-w-3xl lg:max-w-5xl mx-auto px-6 lg:px-0">
                        <p class="text-white text-center font-medium text-sm !mb-0">We use demo data for the mint phases</p>
                    </div>
                </div>
            </main>
        </div>

        <!-- Scripts -->
        <script src="https://cdn.tiny.cloud/1/6zk5wmqbfgxkjqvqyh5f1y44fqollc7y639edh5dt2295z6r/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script src="{{ mix('js/editor.js') }}" defer></script>
    </body>
</html>
