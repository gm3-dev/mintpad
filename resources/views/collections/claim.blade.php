<x-app-layout>
    <x-slot:header>
        <x-back-button href="{{ route('collections.index') }}">{{ __('Back') }}</x-back-button>
    </x-slot:header>

    <div class="bg-white overflow-hidden">
        <input type="hidden" id="collectionID" name="collectionID" :value="{{ $collection->id }}" />

        <div v-if="!wallet.account">
            @include('partials.connect')
        </div>
        <div v-else>
            <form method="POST" action="{{ route('collections.update', $collection->id) }}" enctype="multipart/form-data">
                @method('PUT')

                <div class="text-center mb-10">
                    <h2 class="font-bold text-3xl text-center mb-1">{{ __('Claim phases') }}</h2>
                    <p class="text-gray-500 text-center mb-5">{{ __('Manage your claim phases.') }}</p>
                </div>

                <p v-if="message.error" class="px-6 py-4 rounded-md border border-red-500 mb-4 text-center">@{{ message.error }}</p>

                <div v-for="(phase, index) in claimPhases" class="w-full flex flex-wrap mb-5">
                    <div class="basis-2/3 p-2">
                        <h3 class="basis-full font-bold text-2xl mb-1" v-html="'Phase '+(index+1)"></h3>
                    </div>
                    <div class="basis-1/3 p-2 text-right">
                        <x-button href="#" class="ml-3" @click.prevent="deleteClaimPhase(index)"><i class="fas fa-trash"></i></x-button>
                    </div>
                    <div class="basis-1/3 p-2">
                        <x-label for="start" :value="__('When will this phase start?')" />
                        <x-input id="start" class="mt-1 w-full" type="datetime-local" v-model="phase.startTime" required />
                    </div>
                    <div class="basis-1/3 p-2">
                        <x-label for="max-quantity" :value="__('Number of NFTs in this phase? (0 = unlimited)')" />
                        <x-input id="max-quantity" class="mt-1 w-full" type="number" v-model="phase.maxQuantity" required />
                    </div>
                    <div class="basis-1/3 p-2 relative">
                        <x-label for="price" :value="__('NFT price?')" />
                        <x-input id="price" class="mt-1 w-full" type="text" v-model="phase.price" required />
                        <label v-html="collection.token" class="text-xs absolute right-0 mr-5 mt-4 text-primary-600"></label>
                    </div>
                    <div class="basis-1/3 p-2">
                        <x-label for="max-quantity-transaction" :value="__('Claims per transaction? (0 = unlimited)')" />
                        <x-input id="max-quantity-transaction" class="mt-1 w-full" type="number" v-model="phase.quantityLimitPerTransaction" required />
                    </div>
                    <div class="basis-1/3 p-2">
                        <x-label for="whitelist" :value="__('Who can claim NFTs during this phase?')" />
                        <x-select class="mt-1 !w-full" v-model="phase.whitelist" :options="['Any wallet', 'Only specific wallets']"></x-select>
                    </div>
                    <div v-if="phase.whitelist == 1" class="basis-1/3 p-2">
                        <x-label :value="__('Whitelist')" />
                        <p class="text-sm mt-1"><x-button href="#" class="!text-sm" @click.prevent="toggleWhitelistModal(index, true)">Edit whitelist</x-button><span class="font-bold ml-3" v-html="phase.snapshot.length"></span> addresses in whitelist</p>
                    </div>

                    <div v-if="phase.modal" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                        <div class="fixed z-10 inset-0 overflow-y-auto">
                            <div class="flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0">
                                <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-5xl sm:w-full">
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <table v-if="phase.snapshot != 0" class="w-full">
                                            <tr>
                                                <th>Address</th>
                                                <th>Max claimable</th>
                                            </tr>
                                            <tr v-for="wallet in phase.snapshot">
                                                <td>@{{ wallet.address }}</td>
                                                <td>@{{ phase.quantityLimitPerTransaction }}</td>
                                            </tr>
                                        </table>
                                        <div v-else class="text-center">
                                            <h3 class="font-bold text-2xl mb-1 mt-6">{{ __('Upload whitelist') }}</h3>
                                            <p>Upload a .csv file with a list of addresses. Each row should contain a single address.</p>
                                            <label class="block my-5 text-center">
                                                <span class="sr-only">Choose File</span>
                                                <input type="file" @change="uploadWhitelist($event, index)" class="inline-block text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-600 file:text-white hover:file:bg-primary-700" name="whitelist_file" />
                                            </label>

                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                        <x-button href="#" class="ml-3" @click.prevent="toggleWhitelistModal(index, false)">Close</x-button>
                                        <x-d-button v-if="phase.snapshot != 0" href="#" class="ml-3" @click.prevent="resetWhitelist(index)">Reset list</x-d-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="claimPhases.length == 0">
                    <p class="text-center font-bold">{{ __('No claim phases set yet') }}</p>
                    <p class="text-center">{{ __('Without a claim phase no-one will be able to claim this drop') }}</p>
                </div>
                <div class="text-center w-full mt-5 mx-auto p-2">
                    <x-d-button href="#" class="w-full" @click.prevent="addClaimPhase">Add claim phase</x-d-button>
                </div>
                <div class="px-6 text-center w-full mt-5 sm:max-w-3xl mx-auto">
                    <x-button href="#" class="transaction-button w-1/2" @click.prevent="updateClaimPhases"><i class="fas fa-exchange-alt mr-2"></i> {{ __('Save claim phases') }}</x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
