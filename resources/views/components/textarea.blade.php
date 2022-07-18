@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['rows' => 5, 'class' => 'rounded-sm p-3 shadow-sm text-xs text-gray-900 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:bg-gray-300 disabled:text-gray-500']) !!}>{{ $slot }}</textarea>
