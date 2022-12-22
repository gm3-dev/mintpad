<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="relative">
            <div v-if="!wallet.account">
                <connect-wallet></connect-wallet>
            </div>
            <div v-else>
                <form method="POST" action="{{ route('collections.store') }}" enctype="multipart/form-data">
                    <div class="text-center mb-10">
                        <h1>{{ __('Create NFT collection') }}</h1>
                        <p>{{ __('This is the start of your NFT collection.') }}</p>
                    </div>

                    <!-- <div v-if="collection.chain == 'solana'" class="border border-primary-600 rounded-md p-4 mb-8">
                        <p class="text-sm text-center">To deploy on Solana Devnet, you'll need to manually switch networks on the Developer Settings of your Phantom wallet.</p>
                    </div> -->

                    <x-box class="w-full mb-4">
                        <x-slot name="title">Smart contract settings</x-slot>
                        <x-slot name="tutorial">https://www.youtube.com/embed/NtpD1_uUXEo</x-slot>
                        <x-slot name="content">
                            <div class="w-full flex flex-wrap">
                                <div class="w-full sm:basis-1/3">
                                    <x-label for="symbol" :value="__('Blockchain')" class="relative" info="Choose which blockchain you want to launch your NFT collection on." />
                                    <x-select class="!w-full mb-4" v-model="collection.chain_id" :options="$blockchains"></x-select>
                                </div>
                                <div class="w-full sm:basis-1/3 px-0 sm:px-4">
                                    <x-label for="symbol" :value="__('Symbol / Ticker')" class="relative" info="The symbol of the token contract is the symbol by which the token contract should be known, for example “DOGGY” or “BAYC”. It is broadly equivalent to a stock ticker." />
                                    <x-input id="symbol" class="mb-4" type="text" name="symbol" v-model="collection.symbol" />
                                </div>
                                <div class="w-full sm:basis-1/3">
                                    <x-label for="royalties" :value="__('Creator royalties (%)')" class="relative" info="This is how much percent you want to receive from secondary sales on marketplaces such as OpenSea and Magic Eden." />
                                    <x-input id="royalties" addon="%" class="mb-4" step=".01" min="0" max="100" type="number" name="royalties" v-model="collection.royalties" />
                                </div>
                                <div class="basis-full">
                                    <x-label for="name" :value="__('Collection name')" class="relative" info="This is the name of your NFT collection." />
                                    <x-input id="name" class="mb-4" type="text" name="name" v-model="collection.name" autofocus />
                                </div>
                                <div class="basis-full">
                                    <x-label for="description" :value="__('Collection description')" info="This should be a short description of your collection. This is displayed on marketplaces where people can trade your NFT." />
                                    <x-textarea id="description" class="w-full" name="description" v-model="collection.description"></x-textarea>
                                </div>
                            </div>
                        </x-slot>
                    </x-box>

                    <div class="w-full">
                        <span content="This action will trigger a transaction" v-tippy>
                            <x-button href="#" @click.prevent="deployContract" v-bind:disabled="hasValidChain !== true">{{ __('Deploy smart contract') }}</x-button>
                        </span>
                    </div>            
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
