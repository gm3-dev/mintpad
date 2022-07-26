<x-app-layout>
    <div class="bg-white overflow-hidden">
        <div class="bg-white">
            <div v-if="!wallet.account">
                @include('partials.connect')
            </div>
            <div v-else>
                <div class="text-center mb-10">
                    <h2 class="font-bold text-3xl text-center mb-1">{{ __('Collections') }}</h2>
                    <p class="text-gray-500 text-center mb-5">{{ __('Create and manage your NFT collections.') }}</p>
                    <x-link-button href="{{ route('collections.create') }}">Add Collection</x-link-button>
                </div>
                <div class="font-bold mb-10">

                    @if (count($collections))
                        <div class="px-6 mb-1 text-xs text-gray-500 flex flex-row">
                            <div class="p-2 basis-3/12 lg:basis-4/12">{{ __('Collection name') }}</div>
                            <div class="p-2 basis-1/12">{{ __('Symbol') }}</div>
                            <div class="p-2 basis-1/12">{{ __('Blockchain') }}</div>
                            <div class="p-2 basis-3/12">{{ __('Contract address') }}</div>
                            <div class="p-2 basis-4/12 lg:basis-3/12"></div>
                        </div>
                        @foreach ($collections as $collection)
                            <div class="px-6 py-3 mb-4 rounded-md border border-gray-200 flex flex-row text-left items-center">
                                <div class="p-2 basis-3/12 lg:basis-4/12">{{ $collection->name }}</div>
                                <div class="p-2 basis-1/12">{{ $collection->symbol }}</div>
                                <div class="p-2 basis-1/12">{!! ucfirst($collection->blockchain) !!}</div>
                                <div class="p-2 basis-3/12">
                                    <button href="#" data-tippy-content="Copy address" @click="copyContractAddress" data-address="{{ $collection->address }}" class="border border-gray-200 hover:border-primary-600 px-3 py-2 rounded-md text-xs mr-3"><i class="far fa-copy mr-2"></i>{{ shorten_address($collection->address) }}</button>
                                </div>
                                <div class="p-2 basis-4/12 lg:basis-3/12 text-right">
                                    <x-link-button href="{{ route('collections.edit', $collection->id) }}">Manage</x-link-button>
                                    <x-link-button href="{{ route('collections.claim', $collection->id) }}">Claim</x-link-button>
                                    <x-link-button href="{{ route('collections.collection', $collection->id) }}">Collection</x-link-button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">{{ __('You don\'t have any collections yet!')}}
                    @endif
                </div>

                <div class="text-center mt-10">
                    <h2 class="text-center mb-3">{{ __('Need help with your collection?') }}</h2>
                    <x-link-button href="#">Join our discord</x-link-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
