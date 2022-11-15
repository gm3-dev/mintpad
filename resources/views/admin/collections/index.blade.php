<x-admin-layout>
    <div class="overflow-hidden">
        <div v-if="!wallet.account" class="mb-8">
            <connect-wallet></connect-wallet>
        </div>

        <div class="text-center mb-12">
            <h2 class="text-3xl text-center mb-1 font-semibold">{{ __('Collections') }}</h2>
        </div>
        <div class="mb-10">
            @if (count($collections))
                <div class="px-6 mb-1 text-mintpad-300 flex flex-row text-sm">
                    <div class="p-2 basis-4/12">{{ __('Collection name') }}</div>
                    <div class="p-2 basis-4/12">{{ __('Blockchain') }}</div>
                    <div class="p-2 basis-6/12"></div>
                </div>
                @foreach ($collections as $collection)
                    <div class="px-6 py-1 mb-4 rounded-2xl border-2 border-mintpad-200 bg-primary-100 flex flex-row text-left items-center">
                        <div class="p-2 basis-4/12 font-semibold">{{ $collection->name }}</div>
                        <div class="p-2 basis-4/12 font-semibold">{!! config('blockchains.'.$collection->chain_id.'.full') !!} ({{ config('blockchains.'.$collection->chain_id.'.token') }})</div>
                        <div class="p-2 basis-6/12 text-right">
                            <button href="#" content="Copy contract address" @click="copyContractAddress" data-address="{{ $collection->address }}" class="text-sm border-2 border-mintpad-200 hover:border-primary-600 px-3 py-1 text-mintpad-400 rounded-lg" v-tippy><i class="fas fa-copy mr-2 text-mintpad-300"></i>{{ shorten_address($collection->address, 5, 7) }}</button>
                            <x-blue-button href="{{ route('mint.index', $collection->permalink) }}" target="_blank" class="ml-2 !px-2">{{ __('Mint page') }}</x-blue-button>
                            <span v-if="this.wallet.network.id != {{ $collection->chain_id }}" content="You need to switch to {{ config('blockchains.'.$collection->chain_id.'.token') }}" v-tippy>
                                <x-gray-button href="#" @click.prevent="switchBlockchainTo({{ $collection->chain_id }})" class="ml-2 !px-2 w-24">Switch</x-gray-button>
                            </span>
                            <x-link-button v-else href="#" @click.prevent="openCollectionModal({{ $collection->id }})" class="ml-2 !px-2 w-24">More info</x-link-button>
                            <a href="#" @click.prevent="deleteCollection({{ $collection->id }})" class="ml-2 hover:text-red-700"><i class="fas fa-trash-alt"></i></a>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center">{{ __('No collections found')}}
            @endif
        </div>
    </div>

    <div v-if="modal.show" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end sm:items-center justify-center min-h-full p-4 sm:p-0">
                <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-6xl sm:w-full">
                    <div class="bg-white p-8">
                        <a href="#" class="absolute right-4 top-3 text-3xl text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="modalToggle(false)"><i class="fas fa-times"></i></a>
                        <div class="overflow-y-auto">
                            <h3 v-html="collection.name" class="text-2xl mb-4"></h3>
                            <div class="text-sm text-mintpad-300">
                                <p>Chain: <span class="text-mintpad-500">@{{ chainData.full }} (@{{ chainData.token }}) with ID @{{ chainData.id }}</span></p>
                                <p>Primary sales: <span class="text-mintpad-500">@{{ collection.primary_sales_recipient }}</span></p>
                                <p>Royalties: <span class="text-mintpad-500">@{{ collection.royalties }}% to @{{ collection.fee_recipient }}</span></p>
                                <p>Platform fees: <span class="text-mintpad-500">@{{ collection.platform_fee }}% to @{{ collection.platform_fee_recipient }}</span></p>
                                <p v-if="collection.nfts.length == 0">Total minted: <span class="text-mintpad-500">NFT list empty</span></p>
                                <p v-else>Total minted: <span class="text-mintpad-500">@{{ collection.totalRatio }}% (@{{ collection.totalClaimedSupply}}/@{{ collection.totalSupply }})</span></p>
                                <div class="grid grid-cols-4 mt-2">
                                    <div class="p-1 text-center text-sm" v-for="nft in collection.nfts">
                                        <img class="w-full max-w-max transition-all duration-500 rounded-lg" :src="nft.metadata.image" />
                                    </div>
                                </div> 
                                <pre>
                                    @{{ collection }}
                                </pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
