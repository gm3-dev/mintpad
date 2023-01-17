<x-admin-layout>
    <div class="overflow-hidden">
        <div class="text-center mb-10">
            <h1>{{ __('Collections') }}</h1>
        </div>

        <x-box class="w-full mb-12">
            <x-slot name="title">Your collections</x-slot>
                @if (count($collections))
                    <div>
                        <x-box-row class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                            <div class="basis-1/2 sm:basis-4/12">{{ __('Collection name') }}</div>
                            <div class="hidden sm:block basis-3/12">{{ __('Blockchain') }}</div>
                            <div class="basis-5/12"></div>
                        </x-box-row>
                        @foreach ($collections as $collection)
                            <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                                <div class="basis-full sm:basis-4/12 font-semibold">{{ $collection->name }}</div>
                                <div class="basis-full sm:basis-3/12 font-semibold">{!! config('blockchains.'.$collection->chain_id.'.full') !!} ({{ config('blockchains.'.$collection->chain_id.'.token') }})</div>
                                <div class="basis-full sm:basis-5/12 text-center sm:text-right">
                                    <button href="#" content="Copy contract address" @click="copyContractAddress" data-address="{{ $collection->address }}" class="w-full my-2 sm:my-0 sm:w-auto text-sm g-mintpad-100 dark:bg-mintpad-700 border border-mintpad-200 dark:border-transparent text-mintpad-700 dark:text-gray-200 rounded-md dark:hover:border hover:border-primary-600 dark:hover:border-primary-600 px-3 py-1" v-tippy><i class="fas fa-copy mr-2 text-mintpad-700 dark:text-white"></i>{{ shorten_address($collection->address, 5, 7) }}</button>
                                    <x-blue-button href="{{ route('mint.index', $collection->permalink) }}" target="_blank" class="ml-2 !px-2">{{ __('Mint page') }}</x-blue-button>
                                    <span v-if="this.wallet.network.id != {{ $collection->chain_id }}" content="You need to switch to {{ config('blockchains.'.$collection->chain_id.'.token') }}" v-tippy>
                                        <x-gray-button href="#" @click.prevent="switchBlockchainTo({{ $collection->chain_id }})" class="ml-2 !px-2 w-24">Switch</x-gray-button>
                                    </span>
                                    <x-link-button v-else href="#" @click.prevent="openCollectionModal({{ $collection->id }})" class="ml-2 !px-2 w-24">More info</x-link-button>
                                    <a href="#" @click.prevent="deleteCollection({{ $collection->id }})" class="ml-2 hover:text-red-700"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            </x-box-row>
                        @endforeach
                    </div>
                @else
                    <x-slot name="content">
                        <p>You don't have any collections yet</p>
                    </x-slot>
                @endif
        </x-box>
    </div>

    <div v-if="modal.show" class="relative z-40" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <x-bg-overlay class="!bg-opacity-75"></x-bg-overlay>
        <div class="fixed z-50 inset-0 overflow-y-auto">
            <div class="flex items-end sm:items-center justify-center min-h-full p-4 sm:p-0">
                <div class="relative bg-white dark:bg-mintpad-800 dark:border dark:border-mintpad-900 rounded-md text-left overflow-hidden transform transition-all sm:my-8 sm:max-w-3xl sm:w-full">
                    <div class="border-b border-mintpad-200 dark:border-mintpad-900 font-jpegdev px-10 py-4">
                        <h2 class="!mb-0" v-html="collection.name">Loading...</h2>
                    </div>
                    <div class="bg-white dark:bg-mintpad-800 px-10 py-6">
                        <a href="#" class="absolute right-4 top-3 text-xs font-medium text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="modalToggle(false)">close</a>
                        <div class="overflow-y-auto">
                            <p>Chain: <span class="text-mintpad-500 dark:text-white">@{{ chainData.full }} (@{{ chainData.token }}) with ID @{{ chainData.id }}</span></p>
                            <p>Primary sales: <span class="text-mintpad-500 dark:text-white">@{{ collection.primary_sales_recipient }}</span></p>
                            <p>Royalties: <span class="text-mintpad-500 dark:text-white">@{{ collection.royalties }}% to @{{ collection.fee_recipient }}</span></p>
                            <p>Platform fees: <span class="text-mintpad-500 dark:text-white">@{{ collection.platform_fee }}% to @{{ collection.platform_fee_recipient }}</span></p>
                            <p v-if="collection.nfts.length == 0">Total minted: <span class="text-mintpad-500 dark:text-white">NFT list empty</span></p>
                            <p v-else>Total minted: <span class="text-mintpad-500 dark:text-white">@{{ collection.totalRatio }}% (@{{ collection.totalClaimedSupply}}/@{{ collection.totalSupply }})</span></p>
                            <div class="grid grid-cols-4 mt-2">
                                <div class="p-1 text-center text-sm" v-for="nft in collection.nfts">
                                    <img class="w-full max-w-max transition-all duration-500 rounded-md" :src="nft.metadata.image" />
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
</x-admin-layout>
