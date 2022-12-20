<a {{ $attributes->merge(['class' => 'inline-block font-medium px-4 lg:px-10 py-2.5 text-xs bg-mintpad-200 dark:bg-mintpad-400 text-mintpad-700 dark:text-mintpad-200 hover:text-mintpad-600 disabled:text-mintpad-400 border border-transparent rounded-lg text-sm text-center disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
