<x-app-layout>
    <div class="relative">
        <div v-if="!wallet.account">
            <connect-wallet></connect-wallet>
        </div>
        <div v-else>
            <form method="POST" action="{{ route('collections.store') }}" enctype="multipart/form-data">
                <div class="text-center mb-10">
                    <x-gray-button href="{{ route('collections.index') }}" class="absolute left-0 mt-1">{{ __('Back') }}</x-gray-button>
                    <h2 class="text-3xl text-center mb-1">{{ __('Create NFT collection') }}</h2>
                    <p class="text-center mb-5">{{ __('This is the start of your NFT collection.') }}</p>
                </div>

                <div v-if="collection.chain == 'solana'" class="border-2 border-primary-600 rounded-lg p-4 mb-8">
                    <p class="text-sm text-center">To deploy on Solana Devnet, you'll need to manually switch networks on the Developer Settings of your Phantom wallet.</p>
                </div>

                @include('partials.wallet-messages')

                <div class="w-full flex flex-wrap">
                    <div class="basis-1/3 mb-4">
                        <x-label for="symbol" :value="__('Blockchain')" info="Choose which blockchain you want to launch your NFT collection on." />
                        <x-select class="mt-1 !w-full" v-model="collection.chain_id" :options="$blockchains"></x-select>
                    </div>
                    <div class="basis-1/3 mb-4 px-2">
                        <x-label for="symbol" :value="__('Symbol / Ticker')" info="The symbol of the token contract is the symbol by which the token contract should be known, for example “DOGGY” or “BAYC”. It is broadly equivalent to a stock ticker." />
                        <x-input id="symbol" class="mt-1 w-full" type="text" name="symbol" v-model="collection.symbol" />
                    </div>
                    <div class="basis-1/3 mb-4">
                        <x-label for="royalties" :value="__('Creator royalties (%)')" info="This is how much percent you want to receive from secondary sales on marketplaces such as OpenSea and Magic Eden." />
                        <x-input id="royalties" class="mt-1 w-full" step=".01" min="0" max="100" type="number" name="royalties" v-model="collection.royalties" required />
                    </div>
                    <div class="basis-full mb-4">
                        <x-label for="name" :value="__('Collection name')" info="This is the name of your NFT collection." />
                        <x-input id="name" class="mt-1 w-full" type="text" name="name" v-model="collection.name" required autofocus />
                    </div>
                    <div class="basis-full mb-4">
                        <x-label for="description" :value="__('Collection description')" info="This should be a short description of your collection. This is displayed on marketplaces where people can trade your NFT." />
                        <x-textarea id="description" class="mt-1 w-full" name="description" v-model="collection.description"></x-textarea>
                    </div>
                </div>

                <div class="w-full">
                    <span content="This action will trigger a transaction" v-tippy>
                        <x-button href="#" @click.prevent="deployContract" v-bind:disabled="hasValidChain !== true"><i class="fas fa-cloud-upload-alt mr-2"></i> {{ __('Deploy smart contract') }}</x-button>
                    </span>
                </div>            
            </form>
        </div>
    </div>
</x-app-layout>
