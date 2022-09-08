<x-guest-layout>
    <x-auth-card width="sm:max-w-xl">
        <x-slot name="logo">
            <a href="/" class="text-4xl font-jpegdev">
                mintpad
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <!-- Name -->
                <div>
                    <x-label for="name" :value="__('Name')" class="relative is-required" />
                    <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                </div>
                <!-- Email Address -->
                <div>
                    <x-label for="email" :value="__('Email')" class="relative is-required" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                </div>
                <!-- Password -->
                <div>
                    <x-label for="password" :value="__('Password')" class="relative is-required" />
                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                </div>
                <!-- Confirm Password -->
                <div>
                    <x-label for="password_confirmation" :value="__('Confirm Password')" class="relative is-required" />
                    <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                </div>

                <label for="is-company" class="inline-flex items-center flex-auto pt-1">
                    <x-checkbox id="is-company" type="checkbox" name="is_company" value="1" checked="{{ old('is_company') !== null ? true : false }}" />
                    <x-label for="is-company" class="ml-2">{{ __('I\'m a company') }}</x-label>
                </label>

                <label for="accept-tos" class="inline-flex items-center flex-auto pt-1">
                    <x-checkbox id="accept-tos" type="checkbox" name="accept_tos" value="1" checked="{{ old('accept_tos') !== null ? true : false }}" />
                    <x-label for="accept-tos" class="ml-2 relative is-required">{{ __('I accept the') }} <x-link href="https://mintpad.co/terms-of-service/" target="_blank" class="text-sm">Terms of Service</x-link></x-label>
                </label>
            </div>

            <div id="company-info" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-2">
                <!-- Company name -->
                <div>
                    <x-label for="company_name" :value="__('Company name')" class="relative is-required" />
                    <x-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" />
                </div>
                <!-- VAT ID -->
                <div>
                    <x-label for="vat_id" :value="__('VAT ID number')" class="relative is-required" />
                    <x-input id="vat_id" class="block mt-1 w-full" type="text" name="vat_id" :value="old('vat_id')" />
                </div>
                <!-- Country -->
                <div>
                    <x-label for="country" :value="__('Country')" class="relative is-required" />
                    <x-select id="country" class="block mt-1 !w-full" name="country" :selected="old('country')" :options="$countries"></x-select>
                </div>
                <!-- City -->
                <div>
                    <x-label for="city" :value="__('City')" class="relative is-required" />
                    <x-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" />
                </div>
                <!-- State/Province -->
                <div>
                    <x-label for="state" :value="__('State/Province')" class="relative is-required" />
                    <x-input id="state" class="block mt-1 w-full" type="text" name="state" :value="old('state')" />
                </div>
                <!-- Postal code -->
                <div>
                    <x-label for="postalcode" :value="__('Postal code')" class="relative is-required" />
                    <x-input id="postalcode" class="block mt-1 w-full" type="text" name="postalcode" :value="old('postalcode')" />
                </div>
                <!-- Streetaddress -->
                <div>
                    <x-label for="address" :value="__('Street address')" class="relative is-required" />
                    <x-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
                </div>
                <!-- Street address 2 -->
                <div>
                    <x-label for="address2" :value="__('Street address 2')" />
                    <x-input id="address2" class="block mt-1 w-full" type="text" name="address2" :value="old('address2')" />
                </div>
            </div>

            <div class="w-1/2 mx-auto mt-4">
                <x-button class="w-full">
                    {{ __('Register') }}
                </x-button>
            </div>

            <div>
                <p class="text-xs text-center mt-4">{{ __('Already have an account?') }} <x-link href="{{ route('login') }}">{{ __('Sign in') }}</x-link></p>
            </div>

        </form>
    </x-auth-card>
</x-guest-layout>
