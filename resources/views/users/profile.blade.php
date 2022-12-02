<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="relative mb-12 px-2">
            <div class="text-center mb-12">
                <h2 class="text-center text-3xl mb-1 font-semibold">{{ __('My profile') }}</h2>
                <x-blue-button href="#" class="absolute right-0 top-0 align-middle !rounded-full !px-4 !py-1 !text-xs" @click.prevent="openYouTubeModal('https://www.youtube.com/embed/4HVmvaBeYIs')"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle">{{ __('Watch tutorial') }}</span></x-blue-button>
                <p class="text-center text-lg">{{ __('Enter your details here.') }}</p>
            </div>

            <form method="POST" action="{{ route('users.update') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <x-label for="name" :value="__('Name')" class="relative is-required" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                    </div>
                    <!-- Email Address -->
                    <div>
                        <x-label for="email" :value="__('Email')" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" disabled />
                    </div>
                    <!-- Country -->
                    <div>
                        <x-label for="country" :value="__('Country')" class="relative is-required" />
                        <x-select id="country" class="block mt-1 !w-full" name="country" :selected="old('country', $user->country)" :options="$countries"></x-select>
                    </div>
                    <!-- City -->
                    <div>
                        <x-label for="city" :value="__('City')" class="relative is-required" />
                        <x-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city', $user->city)" required />
                    </div>
                    <!-- State/Province -->
                    <div>
                        <x-label for="state" :value="__('State/Province')" class="relative is-required" />
                        <x-input id="state" class="block mt-1 w-full" type="text" name="state" :value="old('state', $user->state)" required />
                    </div>
                    <!-- Postal code -->
                    <div>
                        <x-label for="postalcode" :value="__('Postal code')" class="relative is-required" />
                        <x-input id="postalcode" class="block mt-1 w-full" type="text" name="postalcode" :value="old('postalcode', $user->postalcode)" required />
                    </div>
                    <!-- Streetaddress -->
                    <div>
                        <x-label for="address" :value="__('Street address')" class="relative is-required" />
                        <x-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $user->address)" required />
                    </div>
                    <!-- Street address 2 -->
                    <div>
                        <x-label for="address2" :value="__('Street address 2')" />
                        <x-input id="address2" class="block mt-1 w-full" type="text" name="address2" :value="old('address2', $user->address2)" />
                    </div>
                </div>

                <!-- Is company -->
                <div class="my-4">
                    <label for="is-company" class="w-1/2">
                        <x-checkbox id="is-company" type="checkbox" name="is_company" value="1" checked="{{ old('is_company', $user->is_company) ? true : false }}" />
                        <x-label for="is-company" class="inline ml-2">{{ __('I\'m a company') }}</x-label>
                    </label>
                </div>

                <div id="company-info" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Company name -->
                    <div>
                        <x-label for="company_name" :value="__('Company name')" class="relative is-required" />
                        <x-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name', $user->company_name)" />
                    </div>
                    <!-- VAT ID -->
                    <div>
                        <x-label for="vat_id" :value="__('VAT ID number')" class="relative is-required" />
                        <x-input id="vat_id" class="block mt-1 w-full" type="text" name="vat_id" :value="old('vat_id', $user->vat_id)" />
                    </div>
                </div>

                <div class="w-full text-center mt-8">
                    <x-button>{{ __('Update') }}</x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
