<x-app-layout>
    <div class="bg-white overflow-hidden">
        <div v-if="!wallet.account">
            @include('partials.connect')
        </div>
        <div v-else>
            <form method="POST" action="{{ route('collections.store') }}" enctype="multipart/form-data">
                <div class="text-center mb-10">
                <x-gray-button href="{{ route('collections.index') }}" class="float-left mt-1">{{ __('Back') }}</x-gray-button>
                    <h2 class="text-3xl text-center mb-1">{{ __('Create NFT collection') }}</h2>
                    <p class="text-mintpad-300 text-center mb-5">{{ __('Create your NFT collections.') }}</p>
                </div>

                <p v-if="message.error" class="px-6 py-4 rounded-md border border-red-500 mb-4 text-center">@{{ message.error }}</p>

                <div class="w-full flex flex-wrap">
                    <div class="basis-full mb-4">
                        <x-label for="name" :value="__('Name')" />
                        <x-input id="name" class="mt-1 w-full" type="text" name="name" v-model="collection.name" required autofocus />
                    </div>
                    <div class="basis-1/3 mb-4">
                        <x-label for="symbol" :value="__('Blockchain')" />
                        <x-select class="mt-1 !w-full" v-model="collection.blockchain" :options="$blockchains"></x-select>
                    </div>
                    <div class="basis-1/3 mb-4 px-2">
                        <x-label for="symbol" :value="__('Symbol')" />
                        <x-input id="symbol" class="mt-1 w-full" type="text" name="symbol" v-model="collection.symbol" />
                    </div>
                    <div class="basis-1/3 mb-4">
                        <x-label for="royalties" :value="__('Royalties (%)')" />
                        <x-input id="royalties" class="mt-1 w-full" step=".01" type="number" name="royalties" v-model="collection.royalties" required />
                    </div>
                    <div class="basis-full mb-4">
                        <x-label for="description" :value="__('Description')" />
                        <x-textarea id="description" class="mt-1 w-full" name="description" v-model="collection.description"></x-textarea>
                    </div>
                </div>

                <div class="w-full">
                    <span content="This action will trigger a transaction" v-tippy>
                        <x-button href="#" @click.prevent="deployContract"><i class="fas fa-exchange-alt mr-2"></i> {{ __('Deploy smart contract') }}</x-button>
                    </span>
                </div>            
            </form>
        </div>
    </div>
</x-app-layout>
