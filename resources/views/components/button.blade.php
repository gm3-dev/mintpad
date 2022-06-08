<button {{ $attributes->merge(['type' => 'submit', 'class' => 'px-4 py-2 bg-primary-600 border border-transparent rounded-md font-bold text-xs text-white text-center tracking-widest hover:bg-primary-700 active:bg-primary-700 focus:outline-none focus:border-primary-700 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
