<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/" class="relative text-4xl font-jpegdev">
                mintpad
                <span class="absolute font-bold text-lg text-primary-600 -bottom-4 -right-3">Beta</span>
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-mintpad-300">
            {{ __('Enter your email address that you used to register. We\'ll send you an email with a link to reset your password.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </div>

            <div class="mt-4">
                <x-button class="w-full">
                    {{ __('Email Password Reset Link') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
