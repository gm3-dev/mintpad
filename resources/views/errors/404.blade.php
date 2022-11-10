<x-minimal-layout>
    <div class="bg-white overflow-hidden text-center">
        <a href="/" class="relative text-4xl font-jpegdev inline-block mt-12">
            mintpad
            @include('partials.beta')
        </a>
        <div class="bg-white">
            <span class="inline-block w-full text-primary-300 mt-12 mb-6 text-9xl text-center">404</span>
            <div class="text-center">
                <h2 class="text-3xl text-center mb-1 font-semibold">{{ __('Page not found') }}</h2>
                <p class="text-center text-lg">{{ __('Are you sure this is the correct URL?') }}</p>
            </div>        
        </div>
    </div>
</x-minimal-layout>
