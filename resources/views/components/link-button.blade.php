<a {{ $attributes->merge(['class' => 'inline-block relative px-3 xl:px-10 py-2 text-xs bg-primary-600 border border-transparent rounded-md text-white text-center hover:bg-primary-700 active:bg-primary-700 focus:outline-none focus:border-primary-700 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
