<x-app-layout>
    <div class="max-w-7xl mx-auto px-6">
        <div class="relative mb-12 px-2">
            <div class="text-center mb-10">
                <h1>{{ __('Your profile') }}</h1>
                <p>{{ __('Update your personal information here.') }}</p>
            </div>

            <form method="POST" action="{{ route('user-profile-information.update') }}">
                @csrf
                @method('PUT')

                <x-box class="w-full">
                    <x-slot name="title">Personal information</x-slot>
                    <x-slot name="content">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                            <!-- Name -->
                            <div>
                                <x-label for="name" :value="__('Name')" class="relative is-required" />
                                <x-input id="name" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            </div>
                            <!-- Email Address -->
                            <div>
                                <x-label for="email" :value="__('Email')" />
                                <x-input id="email" type="email" name="email" :value="old('email', $user->email)" disabled />
                            </div>
                            <!-- Country -->
                            <div>
                                <x-label for="country" :value="__('Country')" class="relative is-required" />
                                <x-select id="country" class="block !w-full" name="country" :selected="old('country', $user->country)" :options="$countries"></x-select>
                            </div>
                            <!-- City -->
                            <div>
                                <x-label for="city" :value="__('City')" class="relative is-required" />
                                <x-input id="city" type="text" name="city" :value="old('city', $user->city)" required />
                            </div>
                            <!-- State/Province -->
                            <div>
                                <x-label for="state" :value="__('State/Province')" class="relative is-required" />
                                <x-input id="state" type="text" name="state" :value="old('state', $user->state)" required />
                            </div>
                            <!-- Postal code -->
                            <div>
                                <x-label for="postalcode" :value="__('Postal code')" class="relative is-required" />
                                <x-input id="postalcode" type="text" name="postalcode" :value="old('postalcode', $user->postalcode)" required />
                            </div>
                            <!-- Streetaddress -->
                            <div>
                                <x-label for="address" :value="__('Street address')" class="relative is-required" />
                                <x-input id="address" type="text" name="address" :value="old('address', $user->address)" required />
                            </div>
                            <!-- Date of birth -->
                            <div>
                                <x-label for="name" :value="__('Date of birth')" />
                                <div class="flex flex-row gap-2">
                                    <div class="basis-1/3">
                                        <x-select id="birth-month" class="!w-full" name="birth_month" :selected="old('birth_month', $user->birth_month)" :options="array_combine(range(1,12),range(1,12))"></x-select>
                                    </div>
                                    <div class="basis-1/3">
                                        <x-select id="birth-day" class="!w-full" name="birth_day" :selected="old('birth_day', $user->birth_day)" :options="array_combine(range(1,31),range(1,31))"></x-select>
                                    </div>
                                    <div class="basis-1/3">
                                        <x-select id="birth-year" class="!w-full" name="birth_year" :selected="old('birth_year', $user->birth_year)" :options="array_combine(range(1920,date('Y')),range(1920,date('Y')))"></x-select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-slot>
                </x-box>

                <x-box class="w-full">
                    <x-slot name="title">Company information</x-slot>
                    <x-slot name="content">
                        <!-- Is company -->
                        <div class="mb-4">
                            <label for="is-company" class="w-1/2">
                                <x-checkbox id="is-company" type="checkbox" name="is_company" value="1" checked="{{ old('is_company', $user->is_company) ? true : false }}" />
                                <x-label for="is-company" class="inline ml-2">{{ __('I\'m a company') }}</x-label>
                            </label>
                        </div>

                        <div id="company-info" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Company name -->
                            <div>
                                <x-label for="company_name" :value="__('Company name')" class="relative is-required" />
                                <x-input id="company_name" type="text" name="company_name" :value="old('company_name', $user->company_name)" />
                            </div>
                            <!-- VAT ID -->
                            <div>
                                <x-label for="vat_id" :value="__('VAT ID number')" class="relative is-required" />
                                <x-input id="vat_id" type="text" name="vat_id" :value="old('vat_id', $user->vat_id)" />
                            </div>
                        </div>
                    </x-slot>
                </x-box>

                @if ($user->role == 'affiliate')
                <x-box class="w-full">
                    <x-slot name="title">Affiliate information</x-slot>
                    <x-slot name="content">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                            <!-- Affiliate code -->
                            <div>
                                <x-label for="affiliate_code" :value="__('Affiliate code')" />
                                <x-input id="affiliate_code" type="text" name="affiliate_code" :value="old('affiliate_code', $user->affiliate_code)" disabled />
                            </div>
                            <!-- Affiliate code -->
                            <div>
                                <x-label for="affiliate_url" :value="__('Your register URL')" />
                                <p class="mt-2"><x-link target="_blank" href="{{ route('register') }}/?affiliate={{ $user->affiliate_code }}">{{ route('register') }}/?affiliate={{ $user->affiliate_code }}</x-link></p>
                            </div>
                        </div>
                    </x-slot>
                </x-box>
                @endif

                <div class="w-full">
                    <x-button>{{ __('Update profile') }}</x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
