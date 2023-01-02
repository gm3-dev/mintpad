<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            @include('partials.logo')
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form id="registration-form" method="POST" action="{{ route('register') }}">
            @csrf
            <h1>Sign Up</h1>
            <div class="flex flex-row gap-2 mb-4">
                <h3 class="basis-1/2 border-b-2 border-primary-200 pb-1 mb-1" :class="{'border-primary-600': signUpStep <= 3}">Personal Info</h3>
                <h3 class="basis-1/2 border-b-2 border-primary-200 pb-1 mb-1" :class="{'border-primary-600': signUpStep > 3}">Account Details</h3>
            </div>

            <div class="">
                <div v-show="signUpStep == 1">
                    <!-- Full name -->
                    <div>
                        <x-label for="name" :value="__('Full name')" />
                        <x-input id="name" type="text" name="name" v-model="form.name" required autofocus />
                    </div>
                    <!-- Date of birth -->
                    <x-label for="name" :value="__('Date of birth')" />
                    <div class="flex flex-row gap-2">
                        <div class="basis-1/3">
                            <x-select id="birth-month" class="!w-full" name="birth_month" v-model="form.birth_month" :options="array_combine(range(1,12),range(1,12))"></x-select>
                        </div>
                        <div class="basis-1/3">
                            <x-select id="birth-day" class="!w-full" name="birth_day" v-model="form.birth_day" :options="array_combine(range(1,31),range(1,31))"></x-select>
                        </div>
                        <div class="basis-1/3">
                            <x-select id="birth-year" class="!w-full" name="birth_year" v-model="form.birth_year" :options="array_combine(range(1920,date('Y')),range(1920,date('Y')))"></x-select>
                        </div>
                    </div>
                    <!-- Reference -->
                    <div>
                        <x-label for="reference" :value="__('How did you hear about us?')" />
                        <x-input id="reference" class="w-full" type="text" name="reference" v-model="form.reference" required />
                    </div>
                    <!-- Company -->
                    <div>
                        <x-label >{{ __('Are you a company?') }}</x-label>
                        <x-radio-group>
                            <x-radio id="is-company-no" type="radio" v-model="form.is_company" value="0" class="inline-block" /><x-label for="is-company-no" class="inline-block mr-2" :value="__('No')" />
                            <x-radio id="is-company-yes" type="radio" v-model="form.is_company" value="1" class="inline-block" /><x-label for="is-company-yes" class="inline-block" :value="__('Yes')" /> 
                        </x-radio-group>      
                    </div>
                </div>
                <div v-show="signUpStep == 2">
                    <!-- Company name -->
                    <div>
                        <x-label for="company_name" :value="__('Company name')" />
                        <x-input id="company_name" class="w-full" type="text" name="company_name" v-model="form.company_name" required />
                    </div>
                    <!-- VAT ID -->
                    <div>
                        <x-label for="vat_id" :value="__('VAT ID number')" />
                        <x-input id="vat_id" class="w-full" type="text" name="vat_id" v-model="form.vat_id" required />
                    </div>
                </div>
                <div v-show="signUpStep == 3">
                    <!-- Country -->
                    <div>
                        <x-label for="country" :value="__('Country')" />
                        <x-select id="country" class="!w-full" name="country" v-model="form.country" :options="$countries"></x-select>
                    </div>
                    <!-- Street address -->
                    <div>
                        <x-label for="address" :value="__('Street address')" />
                        <x-input id="address" type="text" name="address" v-model="form.address" required />
                    </div>
                    <!-- City -->
                    <div>
                        <x-label for="city" :value="__('City')" />
                        <x-input id="city" type="text" name="city" v-model="form.city" required />
                    </div>
                    <div class="flex flex-row gap-2">
                        <!-- Postal code -->
                        <div class="basis-1/2">
                            <x-label for="postalcode" :value="__('Postal code')" />
                            <x-input id="postalcode" class="w-full" type="text" name="postalcode" v-model="form.postalcode" required />
                        </div>
                        <!-- State/Province -->
                        <div class="basis-1/2">
                            <x-label for="state" :value="__('State/Province')" />
                            <x-input id="state" class="w-full" type="text" name="state" v-model="form.state" required />
                        </div>
                    </div>
                </div>
                <div v-show="signUpStep == 4">
                    <!-- Email Address -->
                    <div>
                        <x-label for="email" :value="__('Email Address')" />
                        <x-input id="email" type="email" name="email" v-model="form.email" required />
                    </div>
                    <!-- Password -->
                    <div class="relative">
                        <x-label for="password" :value="__('Password')" />
                        @include('partials.show-password')
                        <x-input id="password" v-bind:type="showPassword ? 'text' : 'password'" name="password" v-model="form.password" required />
                    </div>
                    <!-- Confirm Password -->
                    <div class="relative">
                        <x-label for="password_confirmation" :value="__('Confirm Password')" />
                        @include('partials.show-confirm-password')
                        <x-input id="password_confirmation" class="mb-0" v-bind:type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" v-model="form.password_confirmation" required />
                    </div>

                    <label for="accept-tos" class="relative inline-flex items-center flex-auto mt-4">
                        <x-checkbox id="accept-tos" class="align-top" type="checkbox" name="accept_tos" value="1" v-model="form.accept_tos" />
                        <x-label for="accept-tos" class="ml-2">{{ __('I agree to the') }} <x-link href="https://mintpad.co/terms-of-service/" target="_blank" class="text-xs">Terms of Service</x-link> {{ __('and the') }} <x-link href="https://mintpad.co/privacy-policy/" target="_blank" class="text-xs">Privacy Policy</x-link> {{ __('from Mintpad') }}</x-label>
                        <span v-if="validation.accept_tos && validation.accept_tos != false" class="absolute left-0 -bottom-3 text-red-500 text-xs" v-html="validation.accept_tos"></span>
                    </label>
                </div>
            </div>

            <x-button @click.prevent="nextSignUpStep" class="w-full mt-4">@{{ signUpStep == 4 ? 'Submit' : 'Next' }}</x-button>

            <p class="text-xs text-center mt-4">{{ __('Already have an account?') }} <x-link href="{{ route('login') }}">{{ __('Sign In') }}</x-link></p>

        </form>
    </x-auth-card>
</x-guest-layout>
