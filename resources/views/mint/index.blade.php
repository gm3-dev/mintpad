<x-mint-layout>
    @section('title', $seo['title'])
    @section('description', $seo['description'])
    @section('sharing-image', $seo['image'] ?? false)

    <input type="hidden" id="collectionID" name="collectionID" :value="{{ $collection->id }}" />

    <div id="custom-style-container" class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">

        <div v-if="collection.logo" class="lg:col-span-2 text-center p-3">
            <div class="inline-block max-h-20">
                <img :src="collection.logo" class="h-full max-h-20" />
            </div>
        </div>
        <div v-if="claimPhases.length > 0" class="flex-row grid-cols-1 -my-2">
            <div v-for="(phase, index) in claimPhases" class="relative py-2 h-1/3">
                <div class="bg-white border border-mintpad-200 rounded-md px-8 py-6 h-full" v-bind:class="{'border-2 border-primary-600': phase.active}">
                    <h2 class="text-lg font-semibold mb-1 text-mintpad-500" v-html="phase.name"></h2>
                    <p class="text-sm mb-3 text-mintpad-300">
                        <span v-if="phase.whitelist">• Whitelist <span class="text-primary-600 font-medium" v-html="phase.snapshot.length"></span></span>
                        <span v-if="phase.maxClaimablePerWallet !== 0">• Max <span class="text-primary-600 font-medium" v-html="phase.maxClaimablePerWallet"></span> token</span>
                        • Price <span class="text-primary-600 font-medium" v-html="phase.price"></span> <span class="text-primary-600 font-medium" v-html="collection.token"></span>
                    </p>
                    <div v-if="typeof timers[index] === 'object' && timers[index].state != undefined" class="text-sm flex">
                        <div class="w-1/5 leading-8">
                            <span class="inline-block align-middle text-mintpad-300"><span v-html="timers[index].state"></span> in</span>
                        </div>
                        <div class="w-4/5 text-lg font-semibold text-mintpad-300">
                            <span class="bg-primary-300 text-center rounded inline-block w-10 py-1 mr-1" v-html="timers[index].days">00</span>
                            <span class="bg-primary-300 text-center rounded inline-block w-10 py-1 mr-1" v-html="timers[index].hours">00</span>
                            <span class="bg-primary-300 text-center rounded inline-block w-10 py-1 mr-1" v-html="timers[index].minutes">00</span>
                            <span class="bg-primary-300 text-center rounded inline-block w-10 py-1 mr-1" v-html="timers[index].seconds">00</span>
                        </div>
                    </div>
                    <p v-else-if="timers[index] !== Infinity && typeof timers[index] !== 'object'" class="text-sm text-mintpad-300">{{ __('Phase ended') }}</p>
                    <p v-else class="text-sm text-mintpad-300">&nbsp;</p>
                </div>
            </div>
        </div>
        <div v-else class="grid grid-cols-1 gap-4">
            <div v-for="(phase, index) in [1,2,3]" class="relative bg-white border border-mintpad-200 rounded-md px-8 py-6">
                <div class="bg-gray-300 rounded-md w-1/2 h-5 mb-4 animate-pulse"></div>
                <div class="bg-gray-300 rounded-md w-full h-5 mb-4 animate-pulse"></div>
                <div class="bg-gray-300 rounded-md w-2/3 h-5 animate-pulse"></div>
            </div>
        </div>
        <div class="bg-white border border-mintpad-200 rounded-md text-center">
            <img v-if="collection.image" v-bind:src="collection.image" class="inline-block rounded-md" />
            <i v-else class="far fa-image text-9xl text-primary-300 mb-1 mt-36 rounded-md animate-pulse"></i>
        </div>
        <div class="bg-white border border-mintpad-200 rounded-md p-8">
            <h2 class="text-xl font-semibold text-center mb-1 text-mintpad-500">{{ __('Mint an NFT') }}</h2>
            <p class="font-regular text-center mb-4 text-mintpad-300">{{ __('Start minting by clicking the button below') }}</p>
            <div class="flex gap-2">                    
                <x-button v-if="!wallet.account" @click.prevent="connectMetaMask" class="w-full">Connect MetaMask</x-button>
                <x-button v-else-if="hasValidChain !== true" @click.prevent="switchBlockchainTo(false)" class="w-full">Switch blockchain</x-button>
                <x-button v-else @click.prevent="mintNFT" class="w-full">Start minting</x-button>
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
        <div class="bg-white border border-mintpad-200 rounded-md p-8">
            <h2 class="text-xl font-semibold mb-1 text-mintpad-500" v-html="collection.name"></h2>
            <p class="font-regular text-mintpad-300" v-html="collection.description"></p>
        </div>
        <div v-if="collection.buttons.length" class="lg:col-span-2 bg-white border border-mintpad-200 text-center rounded-md p-6">
            <x-link-button v-for="button in collection.buttons" v-bind:href="button.href" :target="'_blank'" class="m-1" rel="nofollow">@{{ button.label }}</x-link-button>
        </div>
        <div v-if="collection.about || collection.roadmap || collection.team" class="lg:col-span-2 p-4 px-8 bg-white border border-mintpad-200 rounded-md">
            <div class="mb-4 border-b border-mintpad-200">
                <a href="#" v-if="collection.about" @click.prevent="changeTab(1)" class="inline-block text-base -mb-[1px] border-b-2 hover:border-primary-300 mr-4 pb-2 text-mintpad-500" :class="{'border-primary-600': tab == 1}">About the collection</a>
                <a href="#" v-if="collection.roadmap" @click.prevent="changeTab(2)" class="inline-block text-base -mb-[1px] border-b-2 hover:border-primary-300 mr-4 pb-2 text-mintpad-500" :class="{'border-primary-600': tab == 2}">Roadmap</a>
                <a href="#" v-if="collection.team" @click.prevent="changeTab(3)" class="inline-block text-base -mb-[1px] border-b-2 hover:border-primary-300 mr-4 pb-2 text-mintpad-500" :class="{'border-primary-600': tab == 3}">Team</a>
            </div>
            <div v-show="tab == 1 && collection.about" class="tinymce-html" v-html="collection.about"></div>
            <div v-show="tab == 2 && collection.roadmap" class="tinymce-html" v-html="collection.roadmap"></div>
            <div v-show="tab == 3 && collection.team" class="tinymce-html" v-html="collection.team"></div>
        </div>
    </div>
    <div class="my-8 text-center">
        <x-link href="https://mintpad.co/terms-of-service/" target="_blank" class="text-sm bg-white p-2 px-4 rounded-md !text-mintpad-300 ">Terms of Service</x-link>
    </div>
</x-app-layout>
