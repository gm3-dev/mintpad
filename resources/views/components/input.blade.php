@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'rounded-lg px-6 py-3 shadow-sm text-sm text-mintpad-300 dark:text-white border-2 border-mintpad-200 dark:border-gray-600 placeholder:text-gray-400 focus:border-mintpad-200 focus:ring focus:ring-primary-200 dark:focus:ring-gray-600 dark:bg-mintpad-700 disabled:bg-mintpad-100 disabled:text-mintpad-300 dark:disabled:text-mintpad-300 dark:disabled:bg-mintpad-500']) !!}>
