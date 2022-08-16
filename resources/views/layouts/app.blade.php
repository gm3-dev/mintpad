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

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div class="main-container min-h-screen bg-white">
            @include('layouts.navigation')
            @isset($header)
                <div class="p-6 text-left w-full mx-auto bg-gray-100">
                    <div class="px-10 w-full sm:max-w-7xl mx-auto">
                        {{ $header }}
                    </div>
                </div>
            @endisset
            @include('partials.messages')

            <!-- Page Content -->
            <main id="app" data-page="{{ Route::currentRouteName() }}">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </div>
                <div v-if="errorMessage" class="flex fixed w-1/3 bottom-2 inset-x-0 mx-auto p-3 mb-4 bg-red-100 border-t-4 border-red-500 dark:bg-red-200" role="alert">
                    <i class="fas fa-info-circle text-red-700"></i>
                    <div class="ml-3 text-sm font-medium text-red-700" v-html="errorMessage"></div>
                </div>
                <div v-if="successMessage" class="flex fixed w-1/3 bottom-2 inset-x-0 mx-auto p-3 mb-4 bg-green-100 border-t-4 border-green-500 dark:bg-green-200" role="alert">
                    <i class="fas fa-info-circle text-green-700"></i>
                    <div class="ml-3 text-sm font-medium text-green-700" v-html="successMessage"></div>
                </div>
            </main>

            <!-- <div class="p-6 mt-10 text-center w-full mx-auto bg-gray-100">
                <h3 class="text-lg font-bold">Need help? <x-link href="#" class="sm:text-lg">Click here</x-link></h3>
            </div> -->
        </div>
    </body>
</html>
