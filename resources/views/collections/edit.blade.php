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

            <div class="text-center mb-10">
                <h2 class="font-bold text-3xl text-center mb-1">{{ __('Manage NFT collection') }}</h2>
                <p class="text-gray-500 text-center mb-5">{{ __('Manage your NFT collections.') }}</p>
            </div>

            <div class="w-full mx-auto mb-3 pb-3">
                <div class="flex items-center text-sm font-bold border-b border-gray-200">
                    <a href="#" @click.prevent="changeEditTab(1)" class="p-2 px-6 text-gray-700 hover:text-primary-600" :class="{'text-primary-600 border-b-2 border-primary-600': page.tab == 1}">
                        {{ __('Settings') }}
                    </a>
                    <a href="#" @click.prevent="changeEditTab(2)" class="p-2 px-6 text-gray-700 hover:text-primary-600" :class="{'text-primary-600 border-b-2 border-primary-600': page.tab == 2}">
                        {{ __('Claim phases') }}
                    </a>
                    <a href="#" @click.prevent="changeEditTab(3)" class="p-2 px-6 text-gray-700 hover:text-primary-600" :class="{'text-primary-600 border-b-2 border-primary-600': page.tab == 3}">
                        {{ __('Collection') }}
                    </a>
                    <a href="#" @click.prevent="changeEditTab(4)" class="p-2 px-6 text-gray-700 hover:text-primary-600" :class="{'text-primary-600 border-b-2 border-primary-600': page.tab == 4}">
                        {{ __('Embed') }}
                    </a>
                </div>
            </div>
            <div v-if="page.tab == 1">
                <form method="POST" action="{{ route('collections.update', $collection->id) }}" enctype="multipart/form-data">
                    @method('PUT')

                    <p v-if="message.error" class="px-6 py-4 rounded-md border border-red-500 mb-4 text-center">@{{ message.error }}</p>

                    <h3 class="font-bold text-2xl mb-1 mt-6">{{ __('General Settings') }}</h3>
                    <div class="w-full flex flex-wrap">
                        <div class="basis-full p-2">
                            <x-label for="name" :value="__('Name')" />
                            <x-input id="name" class="mt-1 w-full" type="text" name="name" v-model="collection.name" required autofocus />
                        </div>
                        <div class="basis-full p-2">
                            <x-label for="description" :value="__('Description')" />
                            <x-textarea id="description" class="mt-1 w-full" name="description" v-model="collection.description"></x-textarea>
                        </div>
                    </div>
                    <div class="px-6 text-center w-full sm:max-w-3xl mx-auto">
                        <span content="This action will trigger a transaction" v-tippy>
                            <x-button href="#" class="w-1/2" @click.prevent="updateMetadata"><i class="fas fa-exchange-alt mr-2"></i> {{ __('Update general settings') }}</x-button>
                        </span>
                    </div>   

                    <h3 class="font-bold text-2xl mb-1 mt-6">{{ __('Royalties') }}</h3>
                    <div class="w-full flex flex-wrap">
                        <div class="basis-2/3 p-2">
                            <x-label for="fee_recipient" :value="__('Recipient Address')" />
                            <x-input id="fee_recipient" class="mt-1 w-full" type="text" name="fee_recipient" v-model="collection.fee_recipient" />
                        </div>
                        <div class="basis-1/3 p-2">
                            <x-label for="royalties" :value="__('Royalties (%)')" />
                            <x-input id="royalties" class="mt-1 w-full" step=".01" type="number" name="royalties" v-model="collection.royalties" required />
                        </div>
                    </div>
                    <div class="px-6 text-center w-full sm:max-w-3xl mx-auto">
                        <span content="This action will trigger a transaction" v-tippy>
                            <x-button href="#" class="mt-1 w-1/2" @click.prevent="updateRoyalties"><i class="fas fa-exchange-alt mr-2"></i> {{ __('Update royalty settings') }}</x-button>
                        </span>
                    </div>
                </form>
            </div>
            <div v-if="page.tab == 2">
                <form method="POST" action="{{ route('collections.update', $collection->id) }}" enctype="multipart/form-data">
                    @method('PUT')

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
                        <span content="This action will trigger a transaction" v-tippy>
                            <x-button href="#" class="w-1/2" @click.prevent="updateClaimPhases"><i class="fas fa-exchange-alt mr-2"></i> {{ __('Update claim phases') }}</x-button>
                        </span>
                    </div>
                </form>
            </div>
            <div v-if="page.tab == 3">
                <form method="POST" action="{{ route('collections.upload', $collection->id) }}" enctype="multipart/form-data">
                    @csrf

                    <p v-if="message.error" class="px-6 py-4 rounded-md border border-red-500 mb-4 text-center">@{{ message.error }}</p>

                    <div class="text-center w-full sm:max-w-3xl mx-auto">
                        <h3 class="font-bold text-2xl mb-1 mt-6 mb-3">{{ __('Add NFTs to your collection') }}</h3>
                        <label class="block mb-5">
                            <span class="sr-only">Choose File</span>
                            <input type="file" @change="uploadCollection" id="image_collection" class="inline-block text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-600 file:text-white hover:file:bg-primary-700" name="image_collection[]" accept="application/json image/jpeg, image/png, image/jpg, image/gif" directory webkitdirectory mozdirectory multiple/>
                        </label>

                        <div v-if="upload">
                            <p class="text-sm mb-2">We are uploading your collection, please don't close this page or the upload will fail!</p>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-300">
                                <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-1000" v-bind:style="{width: upload.width+'%'}"></div>
                            </div>
                        </div>
                    </div>

                    <div v-if="collection.previews.length > 0" class="text-center">
                        <h3 class="font-bold text-2xl mb-1 mt-6">{{ __('Preview') }}</h3>
                        <div class="grid grid-cols-5">
                            <div v-for="preview in collection.previews">
                                <div class="p-1 text-sm">
                                    <img class="w-full max-w-max transition-all duration-500" :src="preview.image.src" />
                                </div>
                            </div>
                        </div> 
                    </div>

                    <div class="text-center">
                        <h3 class="font-bold text-2xl mb-1 mt-6">{{ __('Your collection') }}</h3>
                        <p v-if="collection.nfts.length == 0">{{ __('Your collection is still empty :(') }}</p>
                        <p v-else>You collection contains <span class="font-bold" v-html="collection.totalSupply"></span> NFTs and <span class="font-bold" v-html="collection.totalClaimedSupply"></span> of them are claimed.</p>
                        <div class="grid grid-cols-5 mt-2">
                            <div class="p-1 text-center text-sm" v-for="nft in collection.nfts">
                                <img class="w-full max-w-max transition-all duration-500" :src="nft.metadata.image" />
                            </div>
                        </div> 
                    </div> 
                </form>
            </div>
            <div v-if="page.tab == 4">
                <h3 class="font-bold text-2xl mt-6 mb-1">{{ __('Embed mint page') }}</h3>
                <div class="w-full flex flex-wrap">
                    <div class="basis-1/2 mx-auto p-2" v-html="ipfs.embed">

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
