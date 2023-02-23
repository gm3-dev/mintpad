<x-editor-layout>
    <div v-if="editMode" class="relative sm:fixed z-40 h-auto sm:h-14 left-0 top-0 p-2 w-full bg-white dark:bg-mintpad-500 border-b border-mintpad-200 dark:border-mintpad-900">
        <div class="max-w-7xl mx-auto px-6 flex flex-wrap gap-4 items-center">
            <div id="color-picker-container" class="grow w-full sm:w-auto text-mintpad-700 relative">
                <a href="#" @click.prevent="toggleColorpicker('primary')" class="vc-open-color-picker inline-block align-middle rounded-md w-7 h-7 border border-gray-200" :style="{backgroundColor: objectToRgba(colorpicker.primary.color, 1)}"></a><span class="text-sm ml-4 align-middle dark:text-mintpad-200">Primary color</span>
                <div v-if="colorpicker.primary.show" class="absolute top-11 left-0">
                    <Chrome v-model="colorpicker.primary.color" :disable-alpha="true"></Chrome>
                </div>
            </div>

            <x-gray-button href="#" class="px-2 sm:!px-4 text-center" @click.prevent="addBackground">Change background</x-gray-button>
            <x-link-button href="#" class="px-2 sm:!px-4 text-center" @click.prevent="updateMintSettings">Publish changes</x-link-button>
            <x-blue-button href="#" class="align-middle !rounded-full px-2 sm:!px-5 !py-1 !text-xs !leading-6" @click.prevent="openYouTubeModal('https://www.youtube.com/embed/Qn2-nY0vZfQ')"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle text-xs">{{ __('Watch tutorial') }}</span></x-blue-button>
        </div>
    </div>
    @include('partials.mint-page')
</x-editor-layout>
