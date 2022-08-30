@props(['checked' => false])

<input {{ $checked ? 'checked' : '' }} {!! $attributes->merge(['class' => 'rounded-md p-2 shadow-sm text-sm text-primary-600 border-2 border-mintpad-200 focus:border-mintpad-200 focus:ring focus:ring-primary-200 disabled:bg-mintpad-200 disabled:text-mintpad-200']) !!}>
