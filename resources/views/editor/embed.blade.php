<x-editor-layout>
    <input type="hidden" id="collectionID" name="collectionID" :value="{{ $collection->id }}" />

    <div v-if="editMode" class="relative sm:fixed z-40 h-auto sm:h-14 left-0 top-0 p-2 w-full bg-white dark:bg-mintpad-500 border-b border-mintpad-200 dark:border-mintpad-900">
        <div class="max-w-7xl mx-auto px-6 flex flex-wrap gap-4 items-center">
            <div class="grow w-full sm:w-auto text-mintpad-700">
                <color-picker v-model="theme.primary" :position="{left: 0, top: '40px'}" :mode="'rgb'"></color-picker><span class="text-sm mx-4 align-middle dark:text-mintpad-200">Primary color</span>
                <color-picker v-model="theme.background" :position="{left: 0, top: '40px'}" :mode="'rgb'"></color-picker><span class="text-sm mx-4 align-middle dark:text-mintpad-200">Background</span>
                <color-picker v-model="theme.phases" :position="{left: 0, top: '40px'}" :mode="'rgb'"></color-picker><span class="text-sm mx-4 align-middle dark:text-mintpad-200">Mint phase background</span>
            </div>

            <x-link-button href="#" class="px-2 sm:!px-4 text-center" @click.prevent="updateMintSettings">Publish changes</x-link-button>
            <x-blue-button href="#" class="align-middle !rounded-full px-2 sm:!px-5 !py-1 !text-xs !leading-6" @click.prevent="openYouTubeModal('https://www.youtube.com/embed/Qn2-nY0vZfQ')"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle text-xs">{{ __('Watch tutorial') }}</span></x-blue-button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 pt-28">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4">
            @include('partials.embed-page')
            <x-box class="mb-0">
                <x-slot name="title">Settings</x-slot>
                <x-slot name="content">
                    <div class="mb-2">
                        <x-checkbox id="settings-phases" class="align-middle" type="checkbox" value="1" v-model="settings.phases" />
                        <x-label for="settings-phases" class="ml-2 mt-1">Show mint phases</x-label>
                    </div>
                    <div class="mb-4">
                        <x-checkbox id="settings-darkmode" class="align-middle" type="checkbox" value="1" v-model="settings.darkmode" />
                        <x-label for="settings-darkmode" class="ml-2 mt-1">Darkmode</x-label>
                    </div>
                    <div>
                        
                        <x-default-button v-if="!settingsChanged" @click.prevent="copyEmbedCode">Copy embed code</x-default-button>
                        <span v-else class="inline-block ml-2" content="Publish your changes first" v-tippy="{ arrow : true }">
                            <x-default-button @click.prevent="copyEmbedCode" disabled="disabled">Copy embed code</x-default-button>
                        </span>
                    </div>
                </x-slot>
            </x-box>
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
</x-editor-layout>
