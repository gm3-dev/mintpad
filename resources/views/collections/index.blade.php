<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="overflow-hidden">
            <div>
                <div class="flex gap-4">
                    <x-box class="basis-1/2 bg-waves bg-cover p-12">
                        <h1>Welcome</h1>
                        <p class="mb-4">Launching a collection can seem complicated. That is why we have made a video where we explain the entire process step by step.</p>
                        <x-link-button href="#" @click.prevent="openYouTubeModal('https://www.youtube.com/embed/Kl8C6RtJmis')">Watch video</x-link-button>
                    </x-box>
                    <x-box class="basis-1/2 text-center p-12">
                        <h1>Let’s get started</h1>
                        <div v-if="!wallet.account">
                            <p>You have to connect your wallet to start creating your collection.</p>
                            <connect-wallet></connect-wallet>
                        </div>
                        <div v-else>
                            <p class="mb-4">We are connected to your wallet.</p>
                            <x-link-button href="{{ route('collections.create') }}">Create collection</x-link-button>
                        </div>
                    </x-box>
                </div>

                <x-box class="w-full mb-12">
                    <x-slot name="title">Your collections</x-slot>
                    <div>
                        @if (count($collections))
                            <x-box-row class="flex flex-row text-sm font-jpegdevmd">
                                <div class="basis-3/12">{{ __('Collection name') }}</div>
                                <div class="basis-2/12">{{ __('Symbol') }}</div>
                                <div class="basis-3/12">{{ __('Blockchain') }}</div>
                                <div class="basis-4/12 lg:basis-2/12">{{ __('Contract address') }}</div>
                                <div class="basis-2/12"></div>
                            </x-box-row>
                            @foreach ($collections as $collection)
                                <x-box-row class="flex flex-row items-center text-mintpad-700 font-medium">
                                    <div class="basis-3/12">{{ $collection->name }}</div>
                                    <div class="basis-2/12">{{ $collection->symbol }}</div>
                                    <div class="basis-3/12">{!! config('blockchains.'.$collection->chain_id.'.full') !!} ({{ config('blockchains.'.$collection->chain_id.'.token') }})</div>
                                    <div class="basis-4/12 lg:basis-2/12">
                                        <button href="#" content="Copy contract address" @click="copyContractAddress" data-address="{{ $collection->address }}" class="text-sm bg-mintpad-100 border border-mintpad-200 text-mintpad-700 hover:border-primary-600 px-3 py-1 dark:text-white rounded-md" v-tippy><i class="fas fa-copy mr-2 text-mintpad-700"></i>{{ shorten_address($collection->address) }}</button>
                                    </div>
                                    <div class="basis-2/12 text-right">
                                        <x-link-button href="{{ route('collections.edit', $collection->id) }}" class="!py-2">Manage</x-link-button>
                                    </div>
                                </x-box-row>
                            @endforeach
                        @endif
                    </div>
                </x-box>

                <div class="text-center mt-10">
                    <x-link-button href="{{ route('collections.create') }}">Create collection</x-link-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
