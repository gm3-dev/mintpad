@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 text-lg leading-5 font-medium text-mintpad-700 dark:text-white transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 text-lg leading-5 font-medium text-mintpad-400 dark:text-gray-200 hover:text-mintpad-700 dark:hover:text-white transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
