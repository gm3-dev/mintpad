<x-app-layout>
    <div class="bg-primary-700 text-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-primary-700">
            
            <form method="POST" action="{{ route('collections.generation', $collection->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="flex gap-4">
                    <div class="mt-4 block w-1/3">
                        <x-label for="collection_size" :value="__('Collection size')" />
                        <x-input id="collection_size" class="block mt-1 w-full" type="number" name="collection_size" :value="$collection->collection_size ?? old('collection_size', 1000)" required autofocus />
                    </div>
                    <div class="mt-4 block w-1/3">
                        <x-label for="base_name" :value="__('Base name')" />
                        <x-input id="base_name" class="block mt-1 w-full" type="text" name="base_name" :value="$collection->base_name ?? old('base_name')" placeholder="NFT name #" required />
                    </div>
                    <div class="mt-4">
                        <x-label for="mint_cost" :value="__('Mint cost (ETH)')" />
                        <x-input id="mint_cost" class="block mt-1 w-full" step=".001" type="number" name="mint_cost" :value="$collection->mint_cost ?? old('mint_cost', 0.05)" required />
                    </div>
                    <div class="mt-4">
                        <x-label for="royalties" :value="__('Royalties (%)')" />
                        <x-input id="royalties" class="block mt-1 w-full" step=".1" type="number" name="royalties" :value="$collection->royalties ?? old('royalties', 0)" required />
                    </div>
                </div>
                <div id="collection-uploader">
                    <input type="file" id="image_collection" @change="uploadCollection" name="image_collection" accept="image/jpeg, image/png, image/jpg, image/gif" directory webkitdirectory mozdirectory multiple>
                    <ul id="listing"></ul>
                </div>
                <div class="mt-4 text-right">
                    <x-button>{{ __('Generate') }}</x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
