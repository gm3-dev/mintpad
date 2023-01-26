<x-minimal-layout>
    <div class="bg-white overflow-hidden text-center">
        <x-slot name="logo">
            @include('partials.logo')
        </x-slot>
        <div class="bg-white">
            <span class="inline-block w-full text-primary-300 mt-12 mb-6 text-9xl text-center">500</span>
            <div class="text-center">
                <h2 class="text-3xl text-center mb-1 font-semibold">{{ __('Something went wrong') }}</h2>
                <p class="text-center text-lg">{{ __('Please try again, or contact support.') }}</p>
            </div>
        </div>
    </div>
</x-minimal-layout>
