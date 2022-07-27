<x-app-layout>
    <x-slot:header>
        <x-back-button href="{{ route('collections.index') }}">{{ __('Back') }}</x-back-button>
    </x-slot:header>
    
    <div class="bg-white overflow-hidden">
        <input type="hidden" id="collectionID" name="collectionID" :value="{{ $collection->id }}" />
        
        <div v-if="!wallet.account">
            @include('partials.connect')
        </div>
        <div v-else>
            <div class="text-center mb-10">
                <h2 class="font-bold text-3xl text-center mb-1">{{ __('Manage NFT collection') }}</h2>
                <p class="text-gray-500 text-center mb-5">{{ __('Your beautiful collection!') }}</p>
            </div>
            
            <form method="POST" action="{{ route('collections.upload', $collection->id) }}" enctype="multipart/form-data">
                @csrf

                <p v-if="message.error" class="px-6 py-4 rounded-md border border-red-500 mb-4 text-center">@{{ message.error }}</p>

                <div class="p-6 text-center w-full sm:max-w-3xl mx-auto">
                    <label class="block mb-5">
                        <span class="sr-only">Choose File</span>
                        <input type="file" @change="uploadCollection" id="image_collection" class="inline-block text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-600 file:text-white hover:file:bg-primary-700" name="image_collection[]" accept="application/json image/jpeg, image/png, image/jpg, image/gif" directory webkitdirectory mozdirectory multiple/>
                    </label>

                    <div v-if="upload">
                        <p class="text-sm mb-2">We are uploading your collection, please don't close this page or the upload will fail!</p>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-300">
                            <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-1000" v-bind:style="{width: upload.width+'%'}"></div>
                        </div>
                    </div>
                </div>


                <!-- <div class="grid grid-cols-4">
                    <div class="p-1" v-for="token in collection">
                        <img class="w-full max-w-max transition-all duration-500" :src="token" />
                    </div>
                </div> -->
                    
            </form>
        </div>
    </div>
</x-app-layout>
