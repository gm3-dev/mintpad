<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/" class="relative text-4xl font-jpegdev">
                mintpad
                @include('partials.beta')
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-mintpad-300">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div class="password relative">
                <x-label for="password" :value="__('Password')" />
                @include('partials.show-password')
                <x-input id="password" class="block mt-1 w-full" v-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="w-full">
                    {{ __('Confirm') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
