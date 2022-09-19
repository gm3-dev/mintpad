@props(['value'])

<label {{ $attributes->merge(['class' => 'inline-block text-sm text-mintpad-400 dark:text-gray-400']) }}>
    {{ $value ?? $slot }}
</label>
