<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            @include('partials.logo')
        </x-slot>

        <div class="mb-4 text-sm text-mintpad-300">
            {{ __('Enter your authentication code to login.') }}
        </div>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('two-factor.login') }}">
            @csrf

            <!-- Password -->
            <div class="password relative">
                <x-label for="code" :value="__('Code')" />
                <x-input id="code" type="text" name="code" required />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="w-full">
                    {{ __('Confirm') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
