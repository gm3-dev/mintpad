<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/" class="relative text-4xl font-jpegdev">
                mintpad
                @include('partials.beta')
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4 relative">
                <x-label for="password" :value="__('Password')" />
                @include('partials.show-password')
                <x-input id="password" class="block mt-1 w-full" v-bind:type="showPassword ? 'text' : 'password'" name="password" required />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4 relative">
                <x-label for="password_confirmation" :value="__('Confirm Password')" />
                @include('partials.show-confirm-password')
                <x-input id="password_confirmation" class="block mt-1 w-full" v-bind:type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required />
            </div>

            <div class="mt-4">
                <x-button class="w-full">
                    {{ __('Reset Password') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
