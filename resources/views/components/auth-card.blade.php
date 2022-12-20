@props(['width' => 'sm:max-w-md'])

<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full {{ $width }} mt-10 p-20 bg-white overflow-hidden sm:rounded">
        {{ $slot }}
    </div>
</div>
