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
        <div class="main-container min-h-screen bg-primary-400">
            <!-- Page Content -->
            <div id="app-loader" class="w-10 mx-auto pt-4 text-lg"><i class="fa-solid fa-gear animate-spin"></i></div>
            <main id="app" class="hidden" data-page="{{ Route::currentRouteName() }}" v-bind:style="style">
                <div class="py-16">
                    <div class="max-w-3xl lg:max-w-5xl mx-auto px-6 lg:px-0">
                        {{ $slot }}
                    </div>
                </div>
                <div v-if="modal.show" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed z-10 inset-0 overflow-y-auto">
                        <div class="flex items-end sm:items-center justify-center min-h-full p-4 sm:p-0">
                            <div class="relative bg-white rounded-lg text-left overflow-hidden transform transition-all sm:my-8 sm:max-w-3xl sm:w-full">
                                <div class="bg-white p-14">
                                    <a href="#" class="absolute right-4 top-3 text-3xl text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="modalToggle(false)"><i class="fas fa-times"></i></a>
                                    <div class="overflow-y-auto">
                                        <h3 v-if="modal.title" v-html="modal.title" class="text-2xl mb-4 mt-6"></h3>
                                        <div v-html="modal.content"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
