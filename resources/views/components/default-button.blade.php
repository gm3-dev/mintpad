<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-block px-4 xl:px-10 py-2 leading-normal border-2 border-mintpad-300 dark:border-gray-200 hover:text-mintpad-500 dark:text-gray-200 rounded-lg text-sm text-mintpad-300 text-center disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
