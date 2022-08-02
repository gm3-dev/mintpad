@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 text-lg leading-5 font-semibold text-mintpad-500 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 text-lg leading-5 font-semibold text-mintpad-300 hover:text-mintpad-500 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
