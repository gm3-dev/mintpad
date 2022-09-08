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
        <script src="{{ mix('js/guest.js') }}" defer></script>
    </head>
    <body>
        <div id="guest-container" class="font-sans antialiased">
            {{ $slot }}
        </div>
        <script type="text/javascript"> var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date(); (function(){ var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0]; s1.async=true; s1.src='https://embed.tawk.to/62cd4911b0d10b6f3e7bec0f/1g7ouaqvr'; s1.charset='UTF-8'; s1.setAttribute('crossorigin','*'); s0.parentNode.insertBefore(s1,s0); })(); </script>
    </body>
</html>
