<a {{ $attributes->merge(['class' => 'inline-block font-medium px-3 xl:px-10 py-2.5 text-xs bg-primary-100 dark:bg-mintpad-700 text-primary-600 dark:text-white box-border border border-primary-600 rounded-md text-sm text-center disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
