<x-app-layout>
    <div class="bg-white overflow-hidden">
        <div class="bg-white">
            <div v-if="!wallet.account">
                @include('partials.connect')
            </div>
            <div v-else>
                <div class="text-center mb-12">
                    <h2 class="text-3xl text-center mb-1 font-semibold">{{ __('Collections') }}</h2>
                    <p class="text-mintpad-300 text-center text-lg">{{ __('Create and manage your NFT collections.') }}</p>
                </div>
                <div class="mb-10">
                    @if (count($collections))
                        <div class="px-6 mb-1 text-mintpad-300 flex flex-row text-sm">
                            <div class="p-2 basis-3/12 lg:basis-4/12">{{ __('Collection name') }}</div>
                            <div class="p-2 basis-1/12">{{ __('Symbol') }}</div>
                            <div class="p-2 basis-2/12">{{ __('Blockchain') }}</div>
                            <div class="p-2 basis-4/12 lg:basis-3/12">{{ __('Contract address') }}</div>
                            <div class="p-2 basis-2/12"></div>
                        </div>
                        @foreach ($collections as $collection)
                            <div class="px-6 py-1 mb-4 rounded-2xl border-2 border-mintpad-200 bg-primary-100 flex flex-row text-left items-center">
                                <div class="p-2 basis-3/12 lg:basis-4/12 font-semibold">{{ $collection->name }}</div>
                                <div class="p-2 basis-1/12 font-semibold">{{ $collection->symbol }}</div>
                                <div class="p-2 basis-2/12 font-semibold">{!! config('blockchains.'.$collection->chain_id.'.full') !!} ({{ config('blockchains.'.$collection->chain_id.'.token') }})</div>
                                <div class="p-2 basis-4/12 lg:basis-3/12">
                                    <button href="#" content="Copy contract address" @click="copyContractAddress" data-address="{{ $collection->address }}" class="text-sm border-2 border-mintpad-200 hover:border-primary-600 px-3 py-1 text-mintpad-400 rounded-lg mr-3" v-tippy><i class="fas fa-copy mr-2 text-mintpad-300"></i>{{ shorten_address($collection->address) }}</button>
                                </div>
                                <div class="p-2 basis-2/12 text-right">
                                    <x-link-button href="{{ route('collections.edit', $collection->id) }}">Manage</x-link-button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">{{ __('You don\'t have any collections yet!')}}
                    @endif
                </div>

                <div class="text-center mt-10">
                    <x-link-button href="{{ route('collections.create') }}">Create Collection</x-link-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
