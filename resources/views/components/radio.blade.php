@props(['disabled' => false])

<input type="radio" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'rounded-lg p-1 text-sm text-mintpad-300 border border-mintpad-200 focus:ring-0 focus:border-mintpad-200 focus:ring focus:ring-primary-200 disabled:bg-mintpad-200 disabled:text-mintpad-300']) !!}>
