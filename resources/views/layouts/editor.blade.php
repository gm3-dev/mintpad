<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>
    <body class="font-sans antialiased">
        <div class="main-container min-h-screen bg-primary-400">
            <!-- Page Content -->
            <div id="app-loader" class="w-10 mx-auto pt-4 text-lg"><i class="fa-solid fa-gear animate-spin"></i></div>
            <main id="app" class="hidden" data-page="{{ Route::currentRouteName() }}" v-bind:style="style">
                <div class="py-16">
                    <div class="max-w-3xl lg:max-w-5xl mx-auto px-6 lg:px-0">
                        {{ $slot }}
                    </div>
                </div>
                @include('partials.messages')
            </main>
        </div>

        <!-- Scripts -->
        <script src="https://cdn.tiny.cloud/1/6zk5wmqbfgxkjqvqyh5f1y44fqollc7y639edh5dt2295z6r/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script src="{{ mix('js/editor.js') }}" defer></script>
    </body>
</html>
