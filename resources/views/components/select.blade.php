@props(['selected' => false, 'options' => []])

<select {!! $attributes->merge(['class' => 'rounded-lg p-6 w-32 px-6 py-3 shadow-sm text-sm text-mintpad-300 border-2 border-mintpad-200 focus:border-mintpad-200 focus:ring focus:ring-primary-200 disabled:bg-mintpad-200 disabled:text-mintpad-200']) !!}>
    @foreach($options as $value => $label)
        <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>