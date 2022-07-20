<button {{ $attributes->merge(['type' => 'submit', 'class' => 'px-4 py-2 bg-white border border-gray-300 rounded-md font-bold text-xs text-black text-center tracking-widest hover:bg-gray-200 active:bg-gray-2000 focus:outline-none focus:border-gray-300 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
