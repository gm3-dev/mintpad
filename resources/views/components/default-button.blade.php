<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-block px-4 xl:px-10 py-2 leading-normal border-2 border-mintpad-300 hover:text-mintpad-500 rounded-lg text-sm text-mintpad-400 text-center tracking-widest disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
