<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            @include('partials.logo')
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <h1>{{ __('Sign In') }}</h1>

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />
                <x-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete />
            </div>

            <!-- Password -->
            <div class="relative">
                <x-label for="password" :value="__('Password')" />
                @include('partials.show-password')
                <x-input id="password" class="mb-0" v-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" />
            </div>

            <!-- Remember Me -->
            <div class="my-5 flex">
                @if (Route::has('password.request'))
                    <x-link class="text-xs" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </x-link>
                @endif
            </div>

            <div>
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
