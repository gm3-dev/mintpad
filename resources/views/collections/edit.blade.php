<x-app-layout>
    <x-slot:header>
        <x-back-button href="{{ route('collections.index') }}">{{ __('Back') }}</x-back-button>
    </x-slot:header>

    <div class="bg-white overflow-hidden">
        <div v-if="!wallet.account">
            @include('partials.connect')
        </div>
        <div v-else>
            <form method="POST" action="{{ route('collections.update', $collection->id) }}" enctype="multipart/form-data">
                @method('PUT')
                <input type="hidden" id="collectionID" name="collectionID" :value="{{ $collection->id }}" />

                <div class="text-center mb-10">
                    <h2 class="font-bold text-3xl text-center mb-1">{{ __('Edit NFT collection') }}</h2>
                    <p class="text-gray-500 text-center mb-5">{{ __('Edit your NFT collections.') }}</p>
                </div>

                <p v-if="message.error" class="px-6 py-4 rounded-md border border-red-500 mb-4 text-center">@{{ message.error }}</p>

                <h3 class="font-bold text-2xl mb-1 mt-6">{{ __('General Settings') }}</h3>
                <div class="w-full flex flex-wrap">
                    <div class="basis-full p-2">
                        <x-label for="name" :value="__('Name')" />
                        <x-input id="name" class="mt-1 w-full" type="text" name="name" v-model="collection.name" required autofocus />
                    </div>
                    <div class="basis-full p-2">
                        <x-label for="description" :value="__('Description')" />
                        <x-textarea id="description" class="mt-1 w-full" name="description" v-model="collection.description"></x-textarea>
                    </div>
                </div>
                <div class="px-6 text-center w-full sm:max-w-3xl mx-auto">
                    <x-button href="#" class="transaction-button ml-4 w-1/2" @click.prevent="updateMetadata"><i class="fas fa-exchange-alt mr-2"></i> {{ __('Update general settings') }}</x-button>
                </div>   

                <h3 class="font-bold text-2xl mb-1 mt-6">{{ __('Royalties') }}</h3>
                <div class="w-full flex flex-wrap">
                    <div class="basis-2/3 p-2">
                        <x-label for="fee_recipient" :value="__('Recipient Address')" />
                        <x-input id="fee_recipient" class="mt-1 w-full" type="text" name="fee_recipient" v-model="collection.fee_recipient" />
                    </div>
                    <div class="basis-1/3 p-2">
                        <x-label for="royalties" :value="__('Royalties (%)')" />
                        <x-input id="royalties" class="mt-1 w-full" step=".01" type="number" name="royalties" v-model="collection.royalties" required />
                    </div>
                </div>
                <div class="px-6 text-center w-full sm:max-w-3xl mx-auto">
                    <x-button href="#" class="transaction-button ml-4 mt-1 w-1/2" @click.prevent="updateRoyalties"><i class="fas fa-exchange-alt mr-2"></i> {{ __('Update royalties') }}</x-button>
                </div>   

                <h3 class="font-bold text-2xl mt-6 mb-1">{{ __('Embed mint page') }}</h3>
                <div class="w-full flex flex-wrap">
                    <div class="basis-1/2 mx-auto p-2" v-html="ipfs.embed">

                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
