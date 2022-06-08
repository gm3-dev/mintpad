<x-app-layout>
    <div class="bg-white overflow-hidden">
        <div class="text-center mb-10">
            <h2 class="font-bold text-3xl text-center mb-1">{{ __('Create NFT collection') }}</h2>
            <p class="text-gray-500 text-center mb-5">{{ __('Create your NFT collections.') }}</p>

            <p class="text-center mb-2 font-bold">{{ __('Select a blockchain') }}</p>
            <x-link-button href="#" @click.prevent="switchBlockchain" class="bg-primary-200 w-28">Solana</x-link-button>
            <x-link-button href="#" @click.prevent="switchBlockchain" class="w-28">Ethereum</x-link-button>
        </div>

        @if (isset($collection))
            <form method="POST" action="{{ route('collections.update', $collection->id) }}" enctype="multipart/form-data">
            @method('PUT')
        @else
            <form method="POST" action="{{ route('collections.store') }}" enctype="multipart/form-data">
        @endif
            @csrf

            <p v-if="message.error" class="px-6 py-4 rounded-md border border-red-500 mb-4 text-center">@{{ message.error }}</p>

            @if (isset($collection) && $collection->deployed)
                <div class="p-6 pb-0">
                    <p class="mb-2">Contract address: <b>{{ $collection->address }}</b></p>
                    <p class="mb-2">
                        <x-label for="base-uri" :value="__('Base URI')" />
                        <x-input id="base-uri" class="mt-1 w-3/4 mr-2" type="text" v-model="form.baseURI" /> <x-link href="#" class="" @click.prevent="setBaseURI">Update Base URI</x-link>
                    </p>

                </div>
            @endif

            <?php dump(input($collection, 'royalties')); ?>
            <div class="flex items-start">
                <div class="flex flex-wrap">
                    <div class="basis-full p-2">
                        <x-label for="name" :value="__('Name')" />
                        <x-input id="name" class="mt-1 w-full" type="text" disabled="{{ $collection->deployed ?? false }}" name="name" :value="$collection->name ?? old('name')" required autofocus />
                    </div>
                    <div class="basis-full p-2">
                        <x-label for="contract_name" :value="__('Contract Name')" />
                        <x-input id="contract_name" class="mt-1 w-full" type="text" disabled="{{ $collection->deployed ?? false }}" name="contract_name" :value="$collection->contract_name ?? old('contract_name')" required />
                    </div>
                    <div class="basis-full p-2">
                        <x-label for="symbol" :value="__('Symbol')" />
                        <x-input id="symbol" class="mt-1 w-full" type="text" disabled="{{ $collection->deployed ?? false }}" name="symbol" :value="$collection->symbol ?? old('symbol')" />
                    </div>
                    <div class="basis-full mt-4 ml-2 grid grid-cols-2">
                        <div>
                            <x-input id="whitelist" class="inline-block p-2 text-primary-600" type="checkbox" disabled="{{ $collection->deployed ?? false }}" name="whitelist" value="1" :checked="isset($collection->whitelist) && $collection->whitelist == 1 ?? old('whitelist')" />
                            <x-label for="whitelist" :value="__('Whitelist')" class="inline-block" />
                        </div>
                        <div>
                            <x-input id="launched" class="inline-block p-2 text-primary-600" type="checkbox" disabled="{{ $collection->deployed ?? false }}" name="launched" value="1" :checked="isset($collection->launched) && $collection->launched == 1 ?? old('launched')" />
                            <x-label for="launched" :value="__('Launch collection later')" class="inline-block" />
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap">
                    <div class="basis-1/2 p-2">
                        <x-label for="collection_size" :value="__('Collection size')" />
                        <x-input id="collection_size" class="mt-1 w-full" type="number" disabled="{{ $collection->deployed ?? false }}" name="collection_size" :value="$collection->collection_size ?? old('collection_size', 1000)" required />
                    </div>
                    <div class="basis-1/2 p-2">
                        <x-label for="royalties" :value="__('Royalties (%)')" />
                        <x-input id="royalties" class="mt-1 w-full" step=".1" type="number" disabled="{{ $collection->deployed ?? false }}" name="royalties" :value="$collection->royalties ?? old('royalties', 0)" required />
                    </div>
                    <div class="basis-1/2 p-2">
                        <x-label for="base_name" :value="__('Base name')" />
                        <x-input id="base_name" class="mt-1 w-full" type="text" disabled="{{ $collection->deployed ?? false }}" name="base_name" :value="$collection->base_name ?? old('base_name')" placeholder="NFT name #" required />
                    </div>
                    <div class="basis-1/2 p-2">
                        <x-label for="mint_cost" :value="__('Mint cost (ETH)')" />
                        <x-input id="mint_cost" class="mt-1 w-full" step=".001" type="number" disabled="{{ $collection->deployed ?? false }}" name="mint_cost" :value="$collection->mint_cost ?? old('mint_cost', 0.05)" required />
                    </div>
                    <div class="mt-8 basis-full p-2">
                        <input type="file" @change="uploadCollection" id="image_collection" name="image_collection[]" accept="image/jpeg, image/png, image/jpg, image/gif" directory webkitdirectory mozdirectory multiple>
                    </div>
                </div>
            </div>

            @if (isset($collection) && $collection->deployed)
                <div class="p-6 rounded-md border border-gray-200">
                    <p class="text-center">This smart contract has been deployed to the blockchain and cannot be changed.</p>
                </div>
            @endif

            <div class="text-center w-full p-6">
                @if (! isset($collection))
                    <x-button>{{ __('Create collection') }}</x-button>
                @elseif (isset($collection) && !$collection->deployed)
                    <x-button>{{ __('Update collection') }}</x-button>
                @endif
            </div>            
        </form>
    </div>
</x-app-layout>
