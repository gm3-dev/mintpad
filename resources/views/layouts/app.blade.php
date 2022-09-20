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
        <div class="main-container min-h-screen bg-white dark:bg-slate-900">
            @include('partials.navigation')
            @isset($header)
                <div class="p-6 text-left w-full mx-auto bg-gray-100">
                    <div class="px-10 w-full sm:max-w-7xl mx-auto">
                        {{ $header }}
                    </div>
                </div>
            @endisset
            @include('partials.messages')

            <!-- Page Content -->
            <div id="app-loader" class="w-10 mx-auto mt-4 text-lg dark:text-white"><i class="fa-solid fa-gear animate-spin"></i></div>
            <main id="app" class="hidden" data-page="{{ Route::currentRouteName() }}">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </div>
                <div v-if="modal.show" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <div class="fixed z-10 inset-0 overflow-y-auto">
                        <div class="flex items-end sm:items-center justify-center min-h-full p-4 sm:p-0">
                            <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-3xl sm:w-full">
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

        <!-- Scripts -->
        <script type="text/javascript" src="{{ asset('js/darkmode.js') }}" defer></script>
        <script src="{{ mix('js/app.js') }}" defer></script>
        <script type="text/javascript"> var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date(); (function(){ var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0]; s1.async=true; s1.src='https://embed.tawk.to/62cd4911b0d10b6f3e7bec0f/1g7ouaqvr'; s1.charset='UTF-8'; s1.setAttribute('crossorigin','*'); s0.parentNode.insertBefore(s1,s0); })(); </script>
    </body>
</html>
