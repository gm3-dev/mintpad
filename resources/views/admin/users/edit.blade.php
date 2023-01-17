<x-admin-layout>
    <div class="overflow-hidden">
        <div class="text-center mb-10">
            <h1>User: {{ $user->name }}</h1>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <x-box class="w-full">
                <x-slot name="title">Settings</x-slot>
                <x-slot name="content">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                        <!-- Role -->
                        <div>
                            <x-label for="role" :value="__('Role')" />
                            <x-select id="role" class="block !w-full" name="role" :selected="old('role', $user->role)" :options="$roles"></x-select>
                        </div>
                        <!-- Status -->
                        <div>
                            <x-label for="status" :value="__('Status')" />
                            <x-select id="status" class="block !w-full" name="status" :selected="old('status', $user->status)" :options="['active' => 'Active', 'blocked' => 'Blocked']"></x-select>
                        </div>
                    </div>
                </x-slot>
            </x-box>

            <x-box class="w-full">
                <x-slot name="title">Personal information</x-slot>
                <x-slot name="content">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                        <!-- Name -->
                        <div>
                            <x-label for="name" :value="__('Name')" />
                            <p>{{ $user->name }}</p>
                        </div>
                        <!-- Email Address -->
                        <div>
                            <x-label for="email" :value="__('Email')" />
                            <p>{{ $user->email }}</p>
                        </div>
                        <!-- Country -->
                        <div>
                            <x-label for="country" :value="__('Country')" />
                            <p>{{ $user->country_name }}</p>
                        </div>
                        <!-- City -->
                        <div>
                            <x-label for="city" :value="__('City')" />
                            <p>{{ $user->city }}</p>
                        </div>
                        <!-- State/Province -->
                        <div>
                            <x-label for="state" :value="__('State/Province')" />
                            <p>{{ $user->state }}</p>
                        </div>
                        <!-- Postal code -->
                        <div>
                            <x-label for="postalcode" :value="__('Postal code')" />
                            <p>{{ $user->postalcode }}</p>
                        </div>
                        <!-- Streetaddress -->
                        <div>
                            <x-label for="address" :value="__('Street address')" />
                            <p>{{ $user->address }}</p>
                        </div>
                        <!-- Date of birth -->
                        <div>
                            <x-label for="name" :value="__('Date of birth')" />
                            <p>{{ date('m/d/Y', strtotime($user->birthday)) }}</p>
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
                            <x-label for="is-company" :value="__('I\'m a company')" />
                            <p>{{ $user->is_company ? 'Yes' : 'No' }}</p>
                        </label>
                    </div>

                    <div id="company-info" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Company name -->
                        <div>
                            <x-label for="company_name" :value="__('Company name')" />
                            <p>{{ $user->company_name ?? '-' }}</p>
                        </div>
                        <!-- VAT ID -->
                        <div>
                            <x-label for="vat_id" :value="__('VAT ID number')" />
                            <p>{{ $user->vat_id ?? '-' }}</p>
                        </div>
                    </div>
                </x-slot>
            </x-box>

            <x-box class="w-full">
                <x-slot name="title">Affiliate information</x-slot>
                <x-slot name="content">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                        @if ($user->role == 'affiliate')
                            <!-- Affiliate code -->
                            <div>
                                <x-label for="affiliate_code" :value="__('Affiliate code')" />
                                <p>{{ $user->affiliate_code ?? '-' }}</p>
                            </div>
                            <!-- Affiliate code -->
                            <div>
                                <x-label for="affiliate_url" :value="__('Your register URL')" />
                                @if ($user->affiliate_code)
                                    <p><x-link target="_blank" href="{{ route('register') }}/?affiliate={{ $user->affiliate_code }}">{{ route('register') }}/?affiliate={{ $user->affiliate_code }}</x-link></p>
                                @else
                                    <p>-</p>
                                @endif
                            </div>
                        @else
                            <p>-</p>
                        @endif
                    </div>
                </x-slot>
            </x-box>

            <div class="w-full">
                <x-button>{{ __('Update') }}</x-button>
            </div>
        </form>
        </div>
</x-admin-layout>
