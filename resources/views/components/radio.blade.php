@props(['disabled' => false])

<input type="radio" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'rounded-lg p-1 shadow-sm text-sm text-mintpad-300 border-2 border-mintpad-200 focus:border-mintpad-200 focus:ring focus:ring-primary-200 disabled:bg-mintpad-100 disabled:text-mintpad-300']) !!}>
