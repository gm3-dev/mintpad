@props(['checked' => false])

<input {{ $checked ? 'checked' : '' }} {!! $attributes->merge(['class' => 'rounded-md p-2 text-sm text-primary-600 border border-primary-200 focus:border-primary-600 disabled:bg-mintpad-200 disabled:text-mintpad-200']) !!}>
