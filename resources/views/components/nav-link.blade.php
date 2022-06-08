@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-primary-600 text-md font-bold leading-5 text-black focus:outline-none focus:border-purple-400 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-md font-bold leading-5 text-black hover:text-gray-700 hover:border-primary-600 focus:outline-none focus:text-primary-300 focus:border-primary-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
