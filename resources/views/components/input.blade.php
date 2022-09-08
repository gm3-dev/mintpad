@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'rounded-lg px-6 py-3 shadow-sm text-sm text-mintpad-300 border-2 border-mintpad-200 placeholder:text-gray-400 focus:border-mintpad-200 focus:ring focus:ring-primary-200 disabled:bg-mintpad-100 disabled:text-mintpad-300']) !!}>
