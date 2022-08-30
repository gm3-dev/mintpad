@props(['width' => 'sm:max-w-sm'])

<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-primary-400">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full {{ $width }} mt-6 px-6 py-6 bg-white shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
