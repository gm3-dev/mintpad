<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-block relative px-4 xl:px-10 py-2 text-sm bg-primary-600 border border-transparent rounded-lg text-white text-center hover:bg-primary-700 active:bg-primary-700 focus:outline-none focus:border-primary-700 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    <i class="absolute left-2 mt-[3px] fa-solid fa-gear animate-spin" style="display: none;"></i>
    {{ $slot }}
</button>
