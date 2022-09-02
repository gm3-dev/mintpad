<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/" class="text-4xl font-jpegdev">
                mintpad
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <h2 class="text-center mb-1 text-xl">{{ __('Login') }}</h2>
                <p class="text-mintpad-300 text-xs text-center mb-4">{{ __('Enter your credentials to access your account.') }}</p>
            </div>

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <!-- Remember Me -->
            <div class="mt-4 flex">
                <label for="remember_me" class="inline-flex items-center flex-auto">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                    <label for="remeber_me" class="ml-2 text-xs">{{ __('Remember me') }}</label>
                </label>

                @if (Route::has('password.request'))
                <div class="text-right text-xs flex-auto">
                    <x-link href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </x-link>
                </div>
                @endif
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="w-full">
                    {{ __('Sign in') }}
                </x-button>
            </div>

            <div>
                <p class="text-xs text-center mt-4">{{ __('Don’t have an account?') }} <x-link href="{{ route('register') }}">{{ __('Sign up') }}</x-link></p>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
