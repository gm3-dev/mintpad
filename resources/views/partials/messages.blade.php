@if ($errors->any() || session('status') || session('error'))
    <div class="px-6 py-3 text-left w-full mx-auto bg-gray-100 border-t border-gray-200">
        <div class="px-10 w-full sm:max-w-7xl mx-auto">
            <p class="font-bold text-xl">Whoops...</p>
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="text-red-500 font-semibold">{{ $error }}</p>
                @endforeach
            @endif
            @if (session('status'))
                <p class="text-red-500 font-semibold">{{ session('status') }}</p>
            @endif
            @if (session('error'))
                <p class="text-red-500 font-semibold">{{ session('error') }}</p>
            @endif
        </div>
    </div>
@endif