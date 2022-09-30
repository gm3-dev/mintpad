@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['rows' => 5, 'class' => 'rounded-lg px-6 py-3 shadow-sm text-sm text-mintpad-300 dark:text-white border-2 border-mintpad-200 dark:border-gray-600 focus:border-mintpad-200 focus:ring focus:ring-primary-200 dark:focus:ring-gray-600 dark:bg-mintpad-700 disabled:bg-mintpad-200 disabled:text-mintpad-200']) !!}>{{ $slot }}</textarea>
