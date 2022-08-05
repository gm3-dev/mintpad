<x-mint-layout>
    <input type="hidden" id="collectionID" name="collectionID" :value="{{ $collection->id }}" />
        
    <div class="text-center mt-4">
        <a href="/" class="text-4xl font-jpegdev">
            mintpad
        </a>
    </div>
    <div v-if="loading" class="text-center mt-12">
        <i class="fas fa-spinner animate-spin text-3xl"></i>
    </div>
    <div v-else>
        <div class="mt-12 grid grid-cols-2 gap-4">
            <div class="grid grid-cols-1 gap-4">
                <div v-for="(phase, index) in claimPhases" class="bg-white rounded-xl px-8 py-6">
                    <h2 class="text-lg font-semibold mb-1"><span v-if="phase.whitelist">{{ __('Whitelist mint #') }}@{{ phase.id }}</span><span v-else>{{ __('Public mint') }}</span></h2>
                    <p class="text-mintpad-300 text-sm mb-4">
                        <span v-if="phase.whitelist">Whitelist <span class="text-primary-600" v-html="phase.snapshot.length"></span> • </span>
                        Max <span class="text-primary-600" v-html="phase.quantityLimitPerTransaction"></span> tokens • 
                        Price <span class="text-primary-600" v-html="phase.price"></span> 
                        <span class="text-primary-600" v-html="collection.token"></span>
                    </p>
                    <p v-if="timers[index]" class="text-sm">
                        <span class="mr-1"><span v-html="timers[index].state"></span> in</span>
                        <span class="bg-primary-300 font-semibold rounded px-1 py-2 mr-1" v-html="timers[index].days">00</span>
                        <span class="bg-primary-300 font-semibold rounded px-1 py-2 mr-1" v-html="timers[index].hours">00</span>
                        <span class="bg-primary-300 font-semibold rounded px-1 py-2 mr-1" v-html="timers[index].minutes">00</span>
                        <span class="bg-primary-300 font-semibold rounded px-1 py-2 mr-1" v-html="timers[index].seconds">00</span>
                    </p>
                    <p v-else class="text-sm">{{ __('Mint phase ended') }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl">
                <img v-if="collection.image" v-bind:src="collection.image" class="rounded-xl" />
            </div>
            <div class="bg-white rounded-xl p-8">
                <h2 class="text-xl font-semibold text-center mb-1">{{ __('Mint an NFT') }}</h2>
                <p class="text-mintpad-300 font-regular text-center mb-4">{{ __('Start minting by clicking the button below') }}</p>
                <div class="flex gap-2">
                    <x-input type="number" v-model="mintAmount" class="w-1/4" />
                    
                    <x-button v-if="!wallet.account" @click.prevent="connectMetaMask" class="w-3/4">Connect MetaMask</x-button>
                    <x-button v-else @click.prevent="mintNFT" class="w-3/4">Start minting</x-button>
                </div>
                <div class="grid grid-cols-2 mt-4 text-sm font-medium text-mintpad-300">
                    <div>
                        Total minted
                    </div>
                    <div class="text-right">
                        @{{ collection.totalRatio }}% (@{{ collection.totalClaimedSupply}}/@{{ collection.totalSupply }})
                    </div>
                </div>
                <div class="w-full mt-2 rounded-full bg-primary-300">
                    <div class="rounded-full bg-primary-600 p-1" v-bind:style="{width: collection.totalRatio+'%'}"></div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-8">
                <h2 class="text-xl font-semibold mb-1" v-html="collection.name"></h2>
                <p class="text-mintpad-300 font-regular" v-html="collection.description"></p>
            </div>
            <div v-if="collection.buttons" class="col-span-2 bg-white text-center rounded-xl p-8">
                <x-gray-button v-for="button in collection.buttons" v-bind:href="button.url" :target="'_blank'" class="mx-1">@{{ button.name }}</x-gray-button>
            </div>
            <div v-if="collection.about" class="col-span-2 bg-white rounded-xl p-8">
                <h2 class="text-xl font-semibold mb-1">{{ __('About the collection') }}</h2>
                <p class="text-mintpad-300 font-regular" v-html="collection.about"></p>
            </div>
        </div>
    </div>
</x-app-layout>
