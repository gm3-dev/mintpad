@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['rows' => 5, 'class' => 'mb-4 rounded-md px-3 py-2 font-regular text-sm text-mintpad-700 dark:text-white bg-primary-100 border border-primary-200 dark:border-gray-600 placeholder:text-gray-400 focus:border-primary-600 dark:focus:border-gray-600 dark:bg-mintpad-700 disabled:bg-mintpad-200 disabled:text-mintpad-300 dark:disabled:text-mintpad-300 dark:disabled:bg-mintpad-500']) !!}>{{ $slot }}</textarea>
