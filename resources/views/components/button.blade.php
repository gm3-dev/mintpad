<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-block px-4 xl:px-10 py-2 text-sm bg-primary-600 border border-transparent rounded-lg text-white text-center hover:bg-primary-700 active:bg-primary-700 focus:outline-none focus:border-primary-700 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
