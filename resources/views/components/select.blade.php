@props(['selected' => false, 'options' => []])

<select {!! $attributes->merge(['class' => 'rounded-sm p-3 w-32 shadow-sm text-xs text-gray-900 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:bg-gray-300 disabled:text-gray-500']) !!}>
    @foreach($options as $value => $label)
        <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>