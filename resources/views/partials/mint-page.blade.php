<input type="hidden" id="collectionID" name="collectionID" :value="{{ $collection->id }}" />

<div v-if="editMode" class="relative sm:fixed z-40 h-auto sm:h-14 left-0 top-0 p-2 w-full bg-white dark:bg-mintpad-500 border-b border-mintpad-200 dark:border-mintpad-900">
    <div class="max-w-7xl mx-auto px-6 flex flex-wrap gap-4 items-center">
        <div class="grow w-full sm:w-auto text-mintpad-700">
            <color-picker v-model="theme.primary" :position="{left: 0, top: '40px'}" :mode="'rgb'"></color-picker><span class="text-sm ml-4 align-middle dark:text-mintpad-200">Primary color</span>
        </div>

        <x-gray-button href="#" class="px-2 sm:!px-4 text-center" @click.prevent="addBackground">Change background</x-gray-button>
        <x-link-button href="#" class="px-2 sm:!px-4 text-center" @click.prevent="updateMintSettings">Publish changes</x-link-button>
        <x-blue-button href="#" class="align-middle !rounded-full px-2 sm:!px-5 !py-1 !text-xs !leading-6" @click.prevent="openYouTubeModal('https://www.youtube.com/embed/Qn2-nY0vZfQ')"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle text-xs">{{ __('Watch tutorial') }}</span></x-blue-button>
    </div>
</div>

<div class="w-full h-96 bg-black/[.35] dark:bg-mintpad-800/[.35] bg-top bg-cover bg-blend-multiply" v-bind:class="{'sm:mt-14': editMode}" v-bind:style="[collection.background ? {backgroundImage: 'url(' + collection.background + ')'} : {}]">
    <div class="relative max-w-7xl mx-auto px-6 pb-4 h-full flex gap-4 items-end">
        <a v-if="editMode" href="#" @click.prevent="addLogo" class="absolute top-4 left-6">
            <img v-if="collection.logo" :src="collection.logo" class="inline-block h-full max-h-10 sm:max-h-16 md:max-h-20 max-w-10 sm:max-w-16 md:max-w-20" content="Edit logo" v-tippy="{placement: 'bottom'}" />
            <x-default-button v-else href="#" class="!px-3"><i class="fa-solid fa-plus mr-2 text-lg align-middle"></i> <span class="align-middle">Add logo</span></x-default-button>
        </a>
        <div v-if="!editMode" class="absolute top-4 left-6">
            <img v-if="collection.logo" :src="collection.logo" class="inline-block h-full max-h-10 sm:max-h-16 md:max-h-20 max-w-10 sm:max-w-16 md:max-w-20" />
        </div>

        <dark-mode class="absolute top-4 right-6"></dark-mode>

        <div class="w-24 sm:w-36 md:w-48 h-24 sm:h-36 md:h-48 bg-white rounded-md p-1 text-center">
            <img v-if="collection.thumb" :src="collection.thumb" class="inline-block rounded-md" />
        </div>
        <h2 class="grow text-lg sm:text-2xl md:text-5xl text-white">{{ $collection->name }}</h2>
    </div>
</div>
<div class="max-w-7xl mx-auto px-6 mt-12">

    <div v-if="claimPhases.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-x-4">
        <x-box v-for="(phase, index) in claimPhases" class="min-h-[12rem]">
            <x-slot name="title">@{{ phase.name }}</x-slot>
            <x-slot name="action"><span v-if="phase.active" class="inline-block absolute top-3.5 right-4 sm:w-auto mx-0 sm:mx-3 px-4 py-1 text-xs border border-green-600 bg-green-100 text-green-600 dark:text-green-600 dark:border-0 dark:bg-[#0F391D] rounded-full">Active</span></x-slot>
            <x-slot name="content">
                <div class="grid grid-cols-1 sm:grid-cols-2">
                    <p v-if="phase.maxClaimableSupply === 0">NFTs: <span class="text-primary-600 mint-text-primary font-medium">unlimited</span></p>
                    <p v-else>NFTs: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.maxClaimableSupply"></span></p>
                    <p>Price: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.price"></span> <span class="text-primary-600 mint-text-primary font-medium" v-html="collection.token"></span></p>
                    <p v-if="phase.maxClaimablePerWallet === 0">Max claims: <span class="text-primary-600 mint-text-primary font-medium">unlimited</span></p>
                    <p v-else>Max claims: <span class="text-primary-600 mint-text-primary font-medium">1</span></p>
                    <p v-if="phase.whitelist">Whitelist: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.snapshot.length"></span></p>
                </div>
                <div v-if="typeof timers[index] === 'object' && timers[index].state != undefined" class="mt-3 text-sm text-mintpad-700">
                    <div class="relative w-full text-right sm:text-center font-regular">
                        <p class="absolute left-0 top-2 font-medium text-mintpad-500"><span v-html="timers[index].state"></span> in</p>
                        <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="timers[index].days">00</span>
                        <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="timers[index].hours">02</span>
                        <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="timers[index].minutes">03</span>
                        <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="timers[index].seconds">04</span>
                    </div>
                </div>
                <p v-else-if="timers[index] !== Infinity && typeof timers[index] !== 'object'" class="mt-6 text-sm text-mintpad-700 font-medium">{{ __('Phase ended') }}</p>
            </x-slot>
        </x-box>
    </div>
    <x-box v-else-if="claimPhases.length == 0 && loadComplete" class="w-full">
        <x-slot name="title">Mint phases</x-slot>
        <x-slot name="content">
            <p>Minting is disabled because no mint phases are active at the moment.</p>
        </x-slot>
    </x-box>
    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-x-4">
        <x-box v-for="(phase, index) in [1,2,3]" class="min-h-[12rem]">
            <x-slot name="title">Phase @{{ index+1 }}</x-slot>
            <x-slot name="content">
                <div class="bg-primary-300 mint-bg-primary-sm rounded-md w-1/2 h-5 mb-4 animate-pulse"></div>
                <div class="bg-primary-300 mint-bg-primary-sm rounded-md w-full h-5 mb-4 animate-pulse"></div>
                <div class="bg-primary-300 mint-bg-primary-sm rounded-md w-2/3 h-5 animate-pulse"></div>
            </x-slot>
        </x-box>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4">
        <x-box class="sm:col-span-2">
            <x-slot name="title">Mint an NFT</x-slot>
            <x-slot name="content">
                <p class="font-regular text-center mb-4">{{ __('Start minting by clicking the button below') }}</p>
                <div v-if="!editMode" class="flex gap-2">                    
                    <x-button v-if="!wallet.account" @click.prevent="connectMetaMask" class="w-full mint-bg-primary">Connect MetaMask</x-button>
                    <x-button v-else-if="hasValidChain !== true" @click.prevent="switchBlockchainTo(false)" class="w-full mint-bg-primary">Switch blockchain</x-button>
                    <x-button v-else @click.prevent="mintNFT" v-bind:disabled="claimPhases.length == 0" class="w-full mint-bg-primary">Start minting</x-button>
                </div>
                <div v-else class="flex gap-2">
                    <x-button @click.prevent="mintNFT" class="w-full mint-bg-primary">Start minting</x-button>
                </div>
                <div class="grid sm:grid-cols-2 mt-4 text-sm font-medium">
                    <div>
                        <p>Total minted</p>
                    </div>
                    <div class="text-right">
                        <p>@{{ collection.totalRatio }}% (@{{ collection.totalClaimedSupply}}/@{{ collection.totalSupply }})</p>
                    </div>
                </div>
                <div class="w-full mt-2 rounded-full bg-primary-300 mint-bg-primary-sm">
                    <div class="rounded-full bg-primary-600 mint-bg-primary p-1" v-bind:style="{width: collection.totalRatio+'%'}"></div>
                </div>
            </x-slot>
        </x-box>
        <x-box>
            <x-slot name="title">Collection details</x-slot>
            <x-slot name="content">
                <div class="grid grid-cols-2 gap-1">
                    <p>Contract address</p><p class="font-medium !text-primary-600 mint-text-primary"><a href="{{ $contract_url }}" target="_blank" class="underline">{{ shorten_address($collection->address) }}</a></p>
                    <p>Collection Size</p><p class="font-medium !text-primary-600 mint-text-primary" v-html="collection.totalSupply">1000</p>
                    <p>Creator Royalties</p><p class="font-medium !text-primary-600 mint-text-primary" v-html="collection.royalties">0.0%</p>
                    <p>Type</p><p class="font-medium !text-primary-600 mint-text-primary">ERC-721</p>
                    <p>Blockchain</p><p class="font-medium !text-primary-600 mint-text-primary" v-html="collection.chainName">Ethereum</p>
                </div>
            </x-slot>
        </x-box>
        <x-box v-if="editMode || collection.buttons.length" class="sm:col-span-3">
            <x-slot name="content">
                <div v-if="editMode">
                    <span class="inline-block" v-for="(button,index) in collection.buttons" content="Edit button" v-tippy>
                        <x-button @click.prevent="editButton(index)" v-bind:href="button.href" :target="'_blank'" class="m-2 mint-bg-primary">@{{ button.label }} <i class="fas fa-edit"></i></x-button>
                    </span>
                    <span class="inline-block" content="Add button" v-tippy>
                        <x-default-button href="#" @click.prevent="newButton" class="!px-3"><i class="fa-solid fa-plus mr-4 text-lg align-middle"></i> <span class="align-middle">Add button</span></x-default-button>
                    </span>
                </div>
                <div v-else>
                    <x-link-button v-for="button in collection.buttons" v-bind:href="button.href" :target="'_blank'" class="m-2 mint-bg-primary" rel="nofollow">@{{ button.label }}</x-link-button>
                </div>
            </x-slot>
        </x-box>
        <x-box class="sm:col-span-3">
            <x-slot name="title">Description</x-slot>
            <x-slot name="content">
                <p class="font-regular">{{ $collection->description }}</p>
            </x-slot>
        </x-box>
    </div>

    <div class="inline-block w-full mt-4 mb-16 text-center">
        <x-link href="https://mintpad.co/terms-of-service/" target="_blank" class="text-sm !text-mintpad-700 dark:!text-mintpad-200 border border-mintpad-200 dark:border-mintpad-900 bg-white dark:bg-mintpad-800 rounded-md p-3 px-6">Terms of Service</x-link>
    </div>
</div>

<div v-if="editMode" class="fixed left-0 bottom-0 p-2 w-full bg-primary-600 text-white">
    <div class="max-w-3xl lg:max-w-5xl mx-auto px-6 lg:px-0">
        <p class="!text-white text-center font-medium text-sm !mb-0">We use demo data for showcase purposes</p>
    </div>
</div>

<div v-if="modal.id == 'mint-successful'">
    <x-modal title="Mint successful!">
        <p>You have an NFT in your wallet! You can now trade this NFT on OpenSea and other marketplaces.</p>
        <p class="!text-primary-600 mint-text-primary">Good luck with trading!</p>
    </x-modal>
</div>
<div v-if="modal.id == 'edit-button'">
    <x-modal title="Edit button">
        <div class="flex gap-2">
            <x-input type="text" v-model="edit.button.label" placeholder="Label" />
            <x-input type="text" v-model="edit.button.href" placeholder="Link" />
        </div>
        <div class="mt-4">
            <span class="inline-block" content="Delete button" v-tippy>
                <x-gray-button href="#" class="!px-4" @click.prevent="deleteButton"><i class="fas fa-trash-alt"></i></x-gray-button>
            </span>
            <span class="float-right inline-block" content="Save" v-tippy>
                <x-button href="#" class="!px-4" @click.prevent="addNewButton">Save</x-button>
            </span>
        </div>
    </x-modal>
</div>
<div v-if="modal.id == 'edit-logo'">
    <x-modal title="Edit logo">
        <div v-if="loadingResource('logo')" class="w-full text-center mb-4">
            <i class="fa-solid fa-cloud-arrow-up animate-bounce mr-2 text-lg"></i> uploading...
        </div>
        <div v-else-if="collection.logo" class="text-center mb-4">
            <img :src="collection.logo" class="inline-block w-auto max-h-40" />
        </div>
        <label v-else for="upload-logo" class="block mb-4">
            <p class="font-regular text-sm mb-1">Uploads are restricted to {{ config('resources.logo.max') }} KB and jpg, jpeg and png.</p>
            <p class="font-regular text-sm mb-2">This logo will be resized to an image with a width of {{ config('resources.logo.width') }} pixels.</p>
            <span class="sr-only">Choose File</span>
            <x-input-file id="upload-logo" @dragenter="dragEnterUploadResource('logo')" @dragleave="dragLeaveUploadResource('logo')" @change="uploadLogo" v-bind:class="resources.logo.classes" accept="image/jpeg, image/png, image/jpg" />
        </label>
        <div class="mt-4">
            <span class="inline-block" content="Delete logo" v-tippy>
                <x-gray-button href="#" class="!px-4" @click.prevent="deleteLogo"><i class="fas fa-trash-alt"></i></x-gray-button>
            </span>
            <span class="float-right inline-block" content="Save" v-tippy>
                <x-button href="#" class="!px-4" @click.prevent="modalClose">Save</x-button>
            </span>
        </div>
    </x-modal>
</div>
<div v-if="modal.id == 'edit-background'">
    <x-modal title="Change background">
        <div v-if="loadingResource('background')" class="w-full text-center mb-4">
            <i class="fa-solid fa-cloud-arrow-up animate-bounce mr-2 text-lg"></i> uploading...
        </div>
        <div v-else-if="collection.background" class="text-center mb-4">
            <img :src="collection.background" class="inline-block w-auto max-h-40" />
        </div>
        <label v-else for="upload-background" class="block mb-4">
            <p class="font-regular text-sm mb-1">Uploads are restricted to {{ config('resources.background.max') }} KB and jpg, jpeg and png.</p>
            <p class="font-regular text-sm mb-2">This background will be resized to an image with a width of {{ config('resources.background.width') }} pixels.</p>
            <span class="sr-only">Choose File</span>
            <x-input-file id="upload-background" @dragenter="dragEnterUploadResource('background')" @dragleave="dragLeaveUploadResource('background')" @change="uploadBackground" v-bind:class="resources.background.classes" accept="image/jpeg, image/png, image/jpg" />
        </label>
        <div class="mt-4">
            <span class="inline-block" content="Delete background" v-tippy>
                <x-gray-button href="#" class="!px-4" @click.prevent="deleteBackground"><i class="fas fa-trash-alt"></i></x-gray-button>
            </span>
            <span class="float-right inline-block" content="Save" v-tippy>
                <x-button href="#" class="!px-4" @click.prevent="modalClose">Save</x-button>
            </span>
        </div>
    </x-modal>
</div>
