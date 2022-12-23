<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-block font-medium bg-white dark:bg-mintpad-800 relative px-4 xl:px-10 py-2.5 border border-primary-200 dark:border-mintpad-900 hover:text-mintpad-500 dark:text-gray-200 rounded-md text-xs text-mintpad-700 text-center disabled:opacity-25 transition ease-in-out duration-150']) }}>
    <i class="absolute left-2 mt-[3px] fa-solid fa-gear animate-spin" style="display: none;"></i>
    {{ $slot }}
</button>
