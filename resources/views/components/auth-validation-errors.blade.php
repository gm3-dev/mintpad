@props(['errors'])

@if ($errors->any())
    <div {{ $attributes }}>
        @foreach ($errors->all() as $error)
            <p class="text-red-600 text-sm text-center mb-1">{{ $error }}</p>
        @endforeach
    </div>
@endif
