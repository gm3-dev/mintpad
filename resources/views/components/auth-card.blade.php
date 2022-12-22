@props(['width' => 'sm:max-w-md'])

<div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 px-6 sm:pt-0">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full {{ $width }} mt-10 p-6 sm:p-20 bg-white dark:bg-mintpad-800 dark:border dark:border-mintpad-700 overflow-hidden rounded-md">
        {{ $slot }}
    </div>
</div>
