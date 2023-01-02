@props(['selected' => false, 'options' => []])

<select {!! $attributes->merge(['class' => 'mb-4 rounded-md p-6 w-32 px-3 py-2 text-sm font-regular text-mintpad-700 dark:text-white bg-primary-100 border-1 border-primary-200 dark:border-mintpad-900 focus:ring-0 focus:border-primary-600 dark:focus:border-gray-600 dark:bg-mintpad-500 disabled:bg-mintpad-200 disabled:text-mintpad-200']) !!}>
    
    @if (count($options) == count($options, COUNT_RECURSIVE))
        @foreach($options as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    @else
        @foreach($options as $group => $group_options)
            <optgroup label="{{ $group }}">
                @foreach($group_options as $value => $label)
                    <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </optgroup>
        @endforeach
    @endif
</select>