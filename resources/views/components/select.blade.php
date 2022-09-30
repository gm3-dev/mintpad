@props(['selected' => false, 'options' => []])

<select {!! $attributes->merge(['class' => 'rounded-lg p-6 w-32 px-6 py-3 shadow-sm text-sm text-mintpad-300 dark:text-white border-2 border-mintpad-200 dark:border-gray-600 focus:border-mintpad-200 dark:focus:ring-gray-600 focus:ring focus:ring-primary-200 dark:bg-mintpad-700 disabled:bg-mintpad-200 disabled:text-mintpad-200']) !!}>
    
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