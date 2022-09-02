<a {{ $attributes->merge(['class' => 'text-primary-600 hover:text-primary-700 hover:underline active:bg-primary-700 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>