<a {{ $attributes->merge(['class' => 'inline-block relative px-4 lg:px-12 py-2 text-sm bg-mintpad-200 dark:bg-mintpad-400 text-mintpad-300 dark:text-mintpad-200 hover:text-mintpad-400 border border-transparent rounded-lg text-sm text-center disabled:opacity-25 transition ease-in-out duration-150']) }}>
    <i class="absolute left-2 mt-[3px] fa-solid fa-gear animate-spin" style="display: none;"></i>
    {{ $slot }}
</a>
