<a {{ $attributes->merge(['class' => 'inline-block px-2 xl:px-4 py-2 bg-gray-400 border border-transparent rounded-md font-bold text-xs text-white text-center tracking-widest hover:bg-gray-500 active:bg-gray-500 focus:outline-none focus:border-gray-500 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    <i class="fas fa-angle-left"></i> {{ $slot }}
</a>
