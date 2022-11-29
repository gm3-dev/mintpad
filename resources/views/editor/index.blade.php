<x-editor-layout>
    <input type="hidden" id="collectionID" name="collectionID" :value="{{ $collection->id }}" />

    <div class="fixed z-40 left-0 top-0 p-2 w-full bg-white border-b-2 border-mintpad-200">
        <div class="max-w-3xl lg:max-w-5xl mx-auto px-6 lg:px-0 flex">
            <div class="grow">
                <color-picker v-model="theme.primary" :position="{left: 0, top: '40px'}" :mode="'rgb'"></color-picker><span class="text-sm mx-2">Primary</span>
                <color-picker v-model="theme.background" :position="{left: 0, top: '40px'}" :mode="'rgb'"></color-picker><span class="text-sm mx-2">Background</span>
                <color-picker v-model="theme.box" :position="{left: 0, top: '40px'}" :mode="'rgb'"></color-picker><span class="text-sm mx-2">Box</span>
                <color-picker v-model="theme.title" :position="{left: 0, top: '40px'}" :mode="'rgb'"></color-picker><span class="text-sm mx-2">Titles</span>
                <color-picker v-model="theme.text" :position="{left: 0, top: '40px'}" :mode="'rgb'"></color-picker><span class="text-sm mx-2">Text</span>
                <x-gray-button href="#" class="!px-4 text-center" @click.prevent="addBackground">Edit background</x-gray-button>
            </div>
            <x-blue-button href="#" class="align-middle !rounded-full !px-4 !py-1 !text-xs mr-2 !leading-6" @click.prevent="openYouTubeModal('https://www.youtube.com/embed/pNR-FvMcvGo')"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle">{{ __('Watch tutorial') }}</span></x-blue-button>
            <x-link-button href="#" class="!px-4 text-center" @click.prevent="updateMintSettings">Publish changes</x-link-button>
        </div>
    </div>

    <div id="custom-style-container" class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="lg:col-span-2 text-center p-3">
            <a href="#" @click.prevent="addLogo" class="inline-block">
                <img v-if="collection.logo" :src="collection.logo" class="inline-block h-full max-h-20" content="Edit logo" v-tippy="{placement: 'bottom'}" />
                <div v-else class="rounded-lg px-6 py-2 border-2 border-gray-300 hover:border-gray-500 hover:text-gray-500 ">
                    <i class="far fa-image text-5xl text-gray-500 mb-1"></i>
                    <p class="text-sm text-gray-500">add logo</p>
                </div>
            </a>
        </div>
        <div class="grid grid-cols-1 gap-4">
            <div class="relative bg-white rounded-xl px-8 py-6">
                <h2 class="text-lg font-semibold mb-1 text-mintpad-500"><span>{{ __('Premium whitelist') }}</span></h2>
                <p class="text-sm mb-3 text-mintpad-300">
                    <span>• Whitelist <span class="text-primary-600">100</span></span>
                    <span>• Max <span class="text-primary-600">1</span> token</span>
                    • Price <span class="text-primary-600">0.1</span> <span class="text-primary-600">ETH</span>
                </p>
                <p class="text-sm text-mintpad-300">{{ __('Phase ended') }}</p>
            </div>
            <div class="relative bg-white rounded-xl px-8 py-6">
                <i class="far fa-check-circle text-primary-600 absolute right-3 top-3 text-xl"></i>
                <h2 class="text-lg font-semibold mb-1 text-mintpad-500"><span>{{ __('Whitelist') }}</span></h2>
                <p class="text-sm mb-3 text-mintpad-300">
                    <span>• Whitelist <span class="text-primary-600">300</span></span>
                    <span>• Max <span class="text-primary-600">1</span> token</span>
                    • Price <span class="text-primary-600">0.2</span> <span class="text-primary-600">ETH</span>
                </p>
                <div class="text-sm flex">
                    <div class="w-1/5">
                        <span class="mr-1 text-mintpad-300"><span>Ends</span> in</span>
                    </div>
                    <div class="w-4/5 text-mintpad-300">
                        <span class="bg-primary-300 font-semibold rounded px-1 py-2 mr-1">00</span>
                        <span class="bg-primary-300 font-semibold rounded px-1 py-2 mr-1">02</span>
                        <span class="bg-primary-300 font-semibold rounded px-1 py-2 mr-1">03</span>
                        <span class="bg-primary-300 font-semibold rounded px-1 py-2 mr-1">04</span>
                    </div>
                </div>
            </div>
            <div class="relative bg-white rounded-xl px-8 py-6">
                <h2 class="text-lg font-semibold mb-1 text-mintpad-500"><span>{{ __('Public') }}</span></h2>
                <p class="text-sm mb-3 text-mintpad-300">
                    <span>• Whitelist <span class="text-primary-600">600</span></span>
                    • Price <span class="text-primary-600">0.5</span> <span class="text-primary-600">ETH</span>
                </p>
                <div class="text-sm flex">
                    <div class="w-1/5">
                        <span class="mr-1 text-mintpad-300"><span>Starts</span> in</span>
                    </div>
                    <div class="w-4/5 text-mintpad-300">
                        <span class="bg-primary-300 font-semibold rounded px-1 py-2 mr-1">00</span>
                        <span class="bg-primary-300 font-semibold rounded px-1 py-2 mr-1">02</span>
                        <span class="bg-primary-300 font-semibold rounded px-1 py-2 mr-1">03</span>
                        <span class="bg-primary-300 font-semibold rounded px-1 py-2 mr-1">04</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl text-center">
            <img v-if="collection.thumb" :src="collection.thumb" class="inline-block rounded-xl" />
            <i v-else class="far fa-image text-9xl text-primary-300 mb-1 mt-36 rounded-xl animate-pulse"></i>
        </div>
        <div class="bg-white rounded-xl p-8">
            <h2 class="text-xl font-semibold text-center mb-1 text-mintpad-500">{{ __('Mint an NFT') }}</h2>
            <p class="font-regular text-center mb-4 text-mintpad-300">{{ __('Start minting by clicking the button below') }}</p>
            <div class="flex gap-2">
                <x-button class="w-full">Start minting</x-button>
            </div>
            <div class="grid grid-cols-2 mt-4 text-sm font-medium text-mintpad-300">
                <div>
                    Total minted
                </div>
                <div class="text-right">
                    50% (500/1000)
                </div>
            </div>
            <div class="w-full mt-2 rounded-full bg-primary-300">
                <div class="rounded-full bg-primary-600 p-1 w-1/2"></div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-8">
            <h2 class="text-xl font-semibold mb-1 text-mintpad-500">{{ $collection->name }}</h2>
            <p class="font-regular text-mintpad-300">{{ $collection->description }}</p>
        </div>
        <div class="lg:col-span-2 bg-white text-center rounded-xl p-8">
            <span v-for="(button,index) in collection.buttons" content="Edit button" v-tippy>
                <x-button @click.prevent="editButton(index)" v-bind:href="button.href" :target="'_blank'" class="m-1">@{{ button.label }} <i class="fas fa-edit"></i></x-button>
            </span>
            <span content="Add button" v-tippy>
                <a href="#" @click.prevent="newButton" class="p-2 rounded-lg text-mintpad-300"><i class="fas fa-plus"></i></a>
            </span>
        </div>
        <div class="lg:col-span-2 p-4 px-8 bg-white rounded-xl">
            <div class="mb-4">
                <a href="#" @click.prevent="changeTab(1)" class="inline-block text-xl mb-4 border-b-4 border-primary-300 mr-12 pb-2 text-mintpad-500" :class="{'border-primary-600': tab == 1}">About the collection</a>
                <a href="#" @click.prevent="changeTab(2)" class="inline-block text-xl mb-4 border-b-4 border-primary-300 mr-12 pb-2 text-mintpad-500" :class="{'border-primary-600': tab == 2}">Roadmap</a>
                <a href="#" @click.prevent="changeTab(3)" class="inline-block text-xl mb-4 border-b-4 border-primary-300 mr-12 pb-2 text-mintpad-500" :class="{'border-primary-600': tab == 3}">Team</a>
            </div>
            <div v-show="tab == 1">
                <tinymce v-model="collection.about"></tinymce>
            </div>
            <div v-show="tab == 2">
                <tinymce v-model="collection.roadmap"></tinymce>
            </div>
            <div v-show="tab == 3">
                <tinymce v-model="collection.team"></tinymce>
            </div>
        </div>
    </div>
    <div class="mt-8 text-center">
        <x-link href="https://mintpad.co/terms-of-service/" target="_blank" class="text-sm bg-white p-2 px-4 rounded-lg !text-mintpad-300 ">Terms of Service</x-link>
    </div>

    <div v-if="modal.id == 'edit-button'">
        <x-modal>
            <div class="flex gap-2">
                <x-input class="w-1/4" type="text" v-model="edit.button.label" placeholder="Label" />
                <x-input class="w-3/4" type="text" v-model="edit.button.href" placeholder="https://www.example.com" />
            </div>
            <div class="mt-4">
                <span content="Delete button" v-tippy>
                    <x-gray-button href="#" class="!px-4" @click.prevent="deleteButton"><i class="fas fa-trash-alt"></i></x-gray-button>
                </span>
                <span class="float-right" content="Close" v-tippy>
                    <x-button href="#" class="!px-4" @click.prevent="modalClose">Close</x-button>
                </span>
            </div>
        </x-modal>
    </div>
    <div v-if="modal.id == 'edit-logo'">
        <x-modal>
            <div v-if="loadingResource('logo')" class="w-full text-center my-10">
                <i class="fa-solid fa-cloud-arrow-up animate-bounce mr-2 text-lg"></i> uploading...
            </div>
            <div v-else-if="collection.logo" class="text-center my-10">
                <img :src="collection.logo" class="inline-block w-auto max-h-40" />
            </div>
            <label v-else for="upload-logo" class="block my-10 text-mintpad-300">
                <p class="font-regular text-sm text-mintpad-300 mb-2">Uploads are restricted to {{ config('resources.logo.max') }} KB and jpg, jpeg and png.</p>
                <span class="sr-only">Choose File</span>
                <input id="upload-logo" type="file" @dragenter="dragEnterUploadResource('logo')" @dragleave="dragLeaveUploadResource('logo')" @change="uploadLogo" class="inline-block p-6 w-full border-2 border-mintpad-200 border-dashed rounded-lg file:mr-2 file:px-4 file:py-3 file:bg-mintpad-200 file:text-mintpad-300 hover:text-mintpad-400 file:rounded-lg file:text-sm file:text-center file:border-0" v-bind:class="resources.logo.classes" accept="image/jpeg, image/png, image/jpg" />
            </label>
            <div class="mt-4">
                <span content="Delete logo" v-tippy>
                    <x-gray-button href="#" class="!px-4" @click.prevent="deleteLogo"><i class="fas fa-trash-alt"></i></x-gray-button>
                </span>
                <span class="float-right" content="Close" v-tippy>
                    <x-button href="#" class="!px-4" @click.prevent="modalClose">Close</x-button>
                </span>
            </div>
        </x-modal>
    </div>
    <div v-if="modal.id == 'edit-background'">
        <x-modal>
            <div v-if="loadingResource('background')" class="w-full text-center my-10">
                <i class="fa-solid fa-cloud-arrow-up animate-bounce mr-2 text-lg"></i> uploading...
            </div>
            <div v-else-if="collection.background" class="text-center my-10">
                <img :src="collection.background" class="inline-block w-auto max-h-40" />
            </div>
            <label v-else for="upload-background" class="block my-10 text-mintpad-300">
                <p class="font-regular text-sm text-mintpad-300 mb-2">Uploads are restricted to {{ config('resources.background.max') }} KB and jpg, jpeg and png.</p>
                <span class="sr-only">Choose File</span>
                <input id="upload-background" type="file" @dragenter="dragEnterUploadResource('background')" @dragleave="dragLeaveUploadResource('background')" @change="uploadBackground" class="inline-block p-6 w-full border-2 border-mintpad-200 border-dashed rounded-lg file:mr-2 file:px-4 file:py-3 file:bg-mintpad-200 file:text-mintpad-300 hover:text-mintpad-400 file:rounded-lg file:text-sm file:text-center file:border-0" v-bind:class="resources.background.classes" accept="image/jpeg, image/png, image/jpg" />
            </label>
            <div class="mt-4">
                <span content="Delete background" v-tippy>
                    <x-gray-button href="#" class="!px-4" @click.prevent="deleteBackground"><i class="fas fa-trash-alt"></i></x-gray-button>
                </span>
                <span class="float-right" content="Close" v-tippy>
                    <x-button href="#" class="!px-4" @click.prevent="modalClose">Close</x-button>
                </span>
            </div>
        </x-modal>
    </div>
    <div class="fixed z-40 left-0 bottom-0 p-2 w-full bg-primary-600 text-white">
        <div class="max-w-3xl lg:max-w-5xl mx-auto px-6 lg:px-0">
            <p class="text-white text-center text-sm">WE USE DEMO DATA FOR THE MINT PHASES</p>
        </div>
    </div>
</x-app-layout>
