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

                <div v-if="errorMessage" class="flex z-50 fixed w-1/3 bottom-2 inset-x-0 mx-auto p-3 mb-4 bg-red-100 border-t-4 border-red-500 dark:bg-red-200" role="alert">
                    <i class="fas fa-info-circle text-red-700"></i>
                    <div class="ml-3 text-sm font-medium text-red-700" v-html="errorMessage"></div>
                </div>
                <div v-if="successMessage" class="flex z-50 fixed w-1/3 bottom-2 inset-x-0 mx-auto p-3 mb-4 bg-green-100 border-t-4 border-green-500 dark:bg-green-200" role="alert">
                    <i class="fas fa-info-circle text-green-700"></i>
                    <div class="ml-3 text-sm font-medium text-green-700" v-html="successMessage"></div>
                </div>
            </main>
        </div>

        <!-- Scripts -->
        <script src="https://cdn.tiny.cloud/1/6zk5wmqbfgxkjqvqyh5f1y44fqollc7y639edh5dt2295z6r/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script src="{{ mix('js/editor.js') }}" defer></script>
    </body>
</html>
