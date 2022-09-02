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
                    <div class="w-full">
                        <i class="fas fa-info-circle text-red-700"></i>
                        <div class="inline ml-3 text-sm font-medium text-red-700" v-html="errorMessage"></div>
                        <a v-if="showRefreshButton" class="text-sm float-right mt-1 text-red-700 underline" href="/{{ request()->path() }}">try again</a>
                    </div>
                </div>
                <div v-if="successMessage" class="flex fixed w-1/3 bottom-2 inset-x-0 mx-auto p-3 mb-4 bg-green-100 border-t-4 border-green-500 dark:bg-green-200" role="alert">
                    <i class="fas fa-info-circle text-green-700"></i>
                    <div class="ml-3 text-sm font-medium text-green-700" v-html="successMessage"></div>
                </div>
                @if ($message = Session::get('success'))
                    <div class="flex fixed w-1/3 bottom-2 inset-x-0 mx-auto p-3 mb-4 bg-green-100 border-t-4 border-green-500 dark:bg-green-200" role="alert">
                        <i class="fas fa-info-circle text-green-700"></i>
                        <div class="ml-3 text-sm font-medium text-green-700">{{ $message }}</div>
                    </div>
                @endif
            </main>
        </div>
        <script type="text/javascript"> var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date(); (function(){ var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0]; s1.async=true; s1.src='https://embed.tawk.to/62cd4911b0d10b6f3e7bec0f/1g7ouaqvr'; s1.charset='UTF-8'; s1.setAttribute('crossorigin','*'); s0.parentNode.insertBefore(s1,s0); })(); </script>
    </body>
</html>
