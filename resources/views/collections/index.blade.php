<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="overflow-hidden">
            <div v-if="!wallet.account">
                <connect-wallet></connect-wallet>
            </div>
            <div v-else>
                <div class="text-center mb-12">
                    <h2 class="text-3xl text-center mb-1 font-semibold">{{ __('Collections') }}</h2>
                    <p class="text-center text-lg">{{ __('Create and manage your NFT collections.') }}</p>
                </div>
                <div class="mb-10">
                    @if (count($collections))
                        <div class="px-6 mb-1 text-mintpad-300 dark:text-gray-200 flex flex-row text-sm">
                            <div class="p-2 basis-3/12 lg:basis-4/12">{{ __('Collection name') }}</div>
                            <div class="p-2 basis-1/12">{{ __('Symbol') }}</div>
                            <div class="p-2 basis-2/12">{{ __('Blockchain') }}</div>
                            <div class="p-2 basis-4/12 lg:basis-3/12">{{ __('Contract address') }}</div>
                            <div class="p-2 basis-2/12"></div>
                        </div>
                        @foreach ($collections as $collection)
                            <div class="px-6 py-1 mb-4 rounded-2xl text-mintpad-500 dark:text-white border-2 border-mintpad-200 dark:border-gray-600 bg-primary-100 dark:bg-mintpad-700 flex flex-row text-left items-center">
                                <div class="p-2 basis-3/12 lg:basis-4/12 font-semibold">{{ $collection->name }}</div>
                                <div class="p-2 basis-1/12 font-semibold">{{ $collection->symbol }}</div>
                                <div class="p-2 basis-2/12 font-semibold">{!! config('blockchains.'.$collection->chain_id.'.full') !!} ({{ config('blockchains.'.$collection->chain_id.'.token') }})</div>
                                <div class="p-2 basis-4/12 lg:basis-3/12">
                                    <button href="#" content="Copy contract address" @click="copyContractAddress" data-address="{{ $collection->address }}" class="text-sm border-2 border-mintpad-200 hover:border-primary-600 px-3 py-1 text-mintpad-400 dark:text-white rounded-lg mr-3" v-tippy><i class="fas fa-copy mr-2 text-mintpad-300"></i>{{ shorten_address($collection->address) }}</button>
                                </div>
                                <div class="p-2 basis-2/12 text-right">
                                    <x-link-button href="{{ route('collections.edit', $collection->id) }}">Manage</x-link-button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="text-center mt-10">
                    <x-link-button href="{{ route('collections.create') }}">Create collection</x-link-button>
                </div>
            </div>
        </div>
    </div>
    <div class="w-full bg-primary-400 py-12 mt-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center">
                <h3 class="text-2xl mb-1">{{ __('Platform overview') }}</h3>
                <p class="text-lg mb-4">{!! __('Launching a collection can seem complicated. That is why we have <br>made a video where we explain the entire process step by step.') !!}</p>
                <iframe class="inline-block" width="650" height="366" src="https://www.youtube.com/embed/95tJuaWhE6g" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</x-app-layout>
