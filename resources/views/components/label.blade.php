@props(['value' => false, 'info' => false])

<label {{ $attributes->merge(['class' => 'inline-block text-xs font-regular mb-1 text-mintpad-400 dark:text-gray-200']) }}>
    {{ $value ? $value : $slot }}
</label>
@if ($info)
<x-more-info content="{{ $info }}"></x-more-info>
@endif