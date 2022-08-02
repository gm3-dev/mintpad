@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm text-mintpad-400']) }}>
    {{ $value ?? $slot }}
</label>
