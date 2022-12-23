@props(['disabled' => false])

@if (isset($attributes['required']))
    <span v-bind:class="{warning: validation.{{ $attributes['name'] }} && validation.{{ $attributes['name'] }} != false}" class="relative inline-flex w-full">
@else
    <span class="relative w-full inline-flex">
@endif
    @if ($addon && $position == 'left')
        <span class="inline-flex mb-4 rounded-l-md font-regular text-sm text-mintpad-700 dark:text-white bg-primary-100 dark:bg-mintpad-500 border border-r-0 border-primary-200 dark:border-mintpad-900">
            <span class="inline-block px-3 my-2 border-r border-primary-200 dark:border-mintpad-900">{{ $addon }}</span>
        </span>
    @endif

    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes.'inline-block w-full mb-4 rounded-md px-3 py-2 font-regular text-sm text-mintpad-700 dark:text-white bg-primary-100 border border-primary-200 dark:border-mintpad-900 placeholder:text-gray-400 focus:ring-0 focus:border-primary-600 dark:focus:border-gray-600 dark:bg-mintpad-500 dark:disabled:bg-mintpad-700 disabled:text-mintpad-300 disabled:border-primary-100 dark:disabled:text-mintpad-300 dark:disabled:bg-mintpad-500']) !!}>
    
    @if ($addon && $position == 'right')
        <span class="inline-flex mb-4 rounded-r-md font-regular text-sm text-mintpad-700 dark:text-white bg-primary-100 dark:bg-mintpad-500 border border-l-0 border-primary-200 dark:border-mintpad-900">
            <span class="inline-block px-3 my-2 border-l border-primary-200 dark:border-mintpad-900">{{ $addon }}</span>
        </span>
    @endif

    @if (isset($attributes['required']))
        <span v-if="validation.{{ $attributes['name'] }} && validation.{{ $attributes['name'] }} != false" class="absolute left-0 -bottom-[2px] text-red-500 text-xs" v-html="validation.{{ $attributes['name'] }}"></span>
    @endif
</span>