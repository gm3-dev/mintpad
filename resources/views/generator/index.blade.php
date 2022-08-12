<x-app-layout>
    <div class="bg-white overflow-hidden">
        <div class="bg-white">
            <div class="text-center mb-12">
                <h2 class="text-3xl text-center mb-1 font-semibold">{{ __('NFT generator') }}</h2>
                <p class="text-mintpad-300 text-center text-lg">{{ __('Generate your own NFT collection.') }}</p>
            </div>

            <div v-if="!generator.layers.length" class="mb-10">
                <label for="generator-traits" class="block my-10 text-mintpad-300">
                    <span class="sr-only">Choose File</span>
                    <input id="generator-traits" type="file" v-bind:class="generator.uploadClasses" @dragenter="dragEnterUploader" @dragleave="dragLeaveUploader" @change="uploadTraits" class="inline-block p-6 w-full border-2 border-mintpad-200 border-dashed rounded-lg file:mr-2 file:px-4 file:py-3 file:bg-mintpad-200 file:text-mintpad-300 hover:text-mintpad-400 file:rounded-lg file:text-sm file:text-center file:border-0" name="image_collection[]" accept="application/json image/jpeg, image/png, image/jpg, image/gif" directory webkitdirectory mozdirectory multiple/>
                </label>
            </div>
            <div v-else>
                <form>
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <x-label class="mb-2">{{ __('Layer order') }}</x-label>
                        <draggable v-model="generator.layers" group="people" @start="onDragStart" @end="onDragEnd">
                            <x-gray-button v-for="(layer, index) in generator.layers" @click.prevent="toggleLayer(index)" href="#" class="w-full !py-3 mb-2 border-2 border-mintpad-200 !text-left" v-bind:class="{'border-primary-600': generator.currentLayer == index}">@{{ layer.type }} <i class="fas fa-grip-vertical text-lg cursor-move float-right"></i></x-gray-button>
                        </draggable>
                    </div>
                    <div>
                        <div class="flex mb-2">
                            <div class="w-2/3 text-sm"><x-label>{{ __('Value') }}</x-label></div>
                            <div class="w-1/3 text-sm"><x-label>{{ __('Weight') }}</x-label></div>
                        </div>
                        <div v-for="(layer, index) in generator.layers" v-if="generator.currentLayer == index" class="w-full">
                            <div v-for="(option, index) in layer.options" class="flex gap-2">
                                <x-input type="text" v-model="option.value" class="w-2/3 mb-2"/>
                                <x-input type="number" v-model="option.weight" pattern="[0-9]*" class="w-1/3 mb-2"/>
                            </div>
                        </div>
                    </div>
                    <div>
                        <x-label for="generator-total" :value="__('The number of NFTs')" class="mb-2 w-full" />
                        <x-input id="generator-total" type="number" v-model="generator.total" class="mb-4 w-full" />

                        <x-label for="generator-base" :value="__('The base name')" class="w-full" />
                        <p class="text-xs text-mintpad-300 mb-3">The NFT title will start with this base name</p>
                        <x-input id="generator-base" type="text" v-model="generator.base" class="mb-4 w-full" />

                        <x-label for="generator-description" :value="__('Description')" class="w-full" />
                        <p class="text-xs text-mintpad-300 mb-3">The description of your NFTs</p>
                        <x-textarea id="generator-description" type="text" v-model="generator.description" :rows="12" class="w-full"></x-textarea>
                    </div>
                </div>

                <div v-if="generator.loader.state != 'idle'" class="text-center w-1/2 mx-auto mt-8">
                    <div v-if="generator.loader.state == 'finished'">
                        <x-gray-button href="{{ route('generator.download') }}" class="w-full">Download</x-gray-button>
                    </div>
                    <div v-else>
                        <div class="mb-1 w-full rounded-lg bg-primary-300">
                            <div class="w-1/2 p-1 bg-primary-600 rounded-lg transition-all" v-bind:style="{'width': generator.loader.progress+'%'}"></div>
                        </div>
                        <p v-html="generator.loader.state"></p>
                    </div>
                </div>

                <div class="text-center mt-8">
                    <x-button @click.prevent="generateCollection">Generate</x-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
