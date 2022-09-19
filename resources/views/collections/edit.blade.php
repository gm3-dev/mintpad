<x-app-layout>
    <div class="relative bg-white overflow-hidden">
        <input type="hidden" id="collectionID" name="collectionID" :value="{{ $collection->id }}" />
        
        <div v-if="!wallet.account">
            @include('partials.connect')
        </div>
        <div v-else>
            <div class="text-center mb-10">
                <x-gray-button href="{{ route('collections.index') }}" class="absolute left-0 mt-1">{{ __('Back') }}</x-gray-button>
                <h2 class="text-3xl text-center mb-1">{{ __('Manage NFT collection') }}</h2>
                <p class="text-mintpad-300 text-center mb-5">{{ __('You can adjust the settings of your collection here.') }}</p>
            </div>

            <div v-if="!hasValidChain" class="border-2 border-primary-600 bg-white rounded-lg p-4 mb-4">
                <p class="text-sm text-center mb-4">Your wallet is not connected to the correct blockchain</p>
                <p class="text-center"><x-link-button href="#" @click.prevent="switchBlockchainTo(false)">Switch blockchain</x-link-button></p>
            </div>
            <div v-else>
                <div class="w-full mx-auto mb-3 pb-3">
                    <div class="flex items-center font-semibold border-b-2 border-mintpad-200">
                        <a href="#" @click.prevent="changeEditTab(1)" class="py-4 mr-10 text-mintpad-300 hover:text-mintpad-500" :class="{'text-mintpad-500 border-b-4 -mb-1 border-primary-600': page.tab == 1}">
                            {{ __('1. Settings') }}
                        </a>
                        <a href="#" @click.prevent="changeEditTab(2)" class="py-4 mr-10 text-mintpad-300 hover:text-mintpad-500" :class="{'text-mintpad-500 border-b-4 -mb-1 border-primary-600': page.tab == 2}">
                            {{ __('2. Mint phases') }}
                        </a>
                        <a href="#" @click.prevent="changeEditTab(3)" class="py-4 mr-10 text-mintpad-300 hover:text-mintpad-500" :class="{'text-mintpad-500 border-b-4 -mb-1 border-primary-600': page.tab == 3}">
                            {{ __('3. Upload collection') }}
                        </a>
                        <a href="#" @click.prevent="changeEditTab(4)" class="py-4 mr-10 text-mintpad-300 hover:text-mintpad-500" :class="{'text-mintpad-500 border-b-4 -mb-1 border-primary-600': page.tab == 4}">
                            {{ __('4. Mint settings') }}
                        </a>
                    </div>
                </div>
                <div v-if="page.tab == 1">
                    <form method="POST" action="{{ route('collections.update', $collection->id) }}" enctype="multipart/form-data">
                        @method('PUT')

                        <h3 class="text-2xl mb-4 mt-6">
                            {{ __('General Settings') }}
                            <x-blue-button href="#" class="ml-6 align-middle !rounded-full !px-4 !py-1 !text-xs" @click.prevent="openYouTubeModal('https://www.youtube.com/embed/RLKfq9vb9AQ')"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle">{{ __('Watch tutorial') }}</span></x-blue-button>
                        </h3>
                        <div class="w-full flex flex-wrap">
                            <div class="basis-full mb-4">
                                <x-label for="name" :value="__('Collection name')" />
                                <x-input id="name" class="mt-1 w-full" type="text" name="name" v-model="collection.name" required autofocus />
                            </div>
                            <div class="basis-full mb-4">
                                <x-label for="description" :value="__('Collection description')" />
                                <x-textarea id="description" class="mt-1 w-full" name="description" v-model="collection.description"></x-textarea>
                            </div>
                        </div>
                        <div class="w-full">
                            <span content="This action will trigger a transaction" v-tippy>
                                <x-button href="#" @click.prevent="updateMetadata"><i class="fas fa-cloud-upload-alt mr-2"></i> {{ __('Update settings') }}</x-button>
                            </span>
                        </div>   

                        <h3 class="text-2xl mb-4 mt-6">{{ __('Royalties') }}</h3>
                        <div class="w-full flex flex-wrap">
                            <div class="basis-2/3 mb-4 pr-2">
                                <x-label for="fee_recipient" :value="__('Recipient Address')" />
                                <p class="text-mintpad-300 font-regular text-sm mb-1">A recipient address is the wallet address of the recipient of the royalties.</p>
                                <x-input id="fee_recipient" class="mt-1 w-full" type="text" name="fee_recipient" v-model="collection.fee_recipient" />
                            </div>
                            <div class="basis-1/3">
                                <x-label for="royalties" :value="__('Royalties (%)')" />
                                <p class="text-mintpad-300 font-regular text-sm mb-1">What percentage you want to receive.</p>
                                <x-input id="royalties" class="mt-1 w-full" step=".01" type="number" name="royalties" v-model="collection.royalties" required />
                            </div>
                        </div>
                        <div class="w-full">
                            <span content="This action will trigger a transaction" v-tippy>
                                <x-button href="#" @click.prevent="updateRoyalties"><i class="fas fa-cloud-upload-alt mr-2"></i> {{ __('Update royalties') }}</x-button>
                            </span>
                        </div>
                    </form>
                </div>
                <div v-if="page.tab == 2">
                    <form method="POST" action="{{ route('collections.update', $collection->id) }}" enctype="multipart/form-data">
                        @method('PUT')

                        <h3 class="text-2xl mb-4 mt-6">
                            {{ __('Mint phases') }} 
                            <x-blue-button href="#" class="ml-6 align-middle !rounded-full !px-4 !py-1 !text-xs" @click.prevent="openYouTubeModal('https://www.youtube.com/embed/wSGxNAaQaT0')"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle">{{ __('Watch tutorial') }}</span></x-blue-button>
                        </h3>
                        <p class="text-mintpad-300 font-regular text-sm">{{ __('On this page you can set mint phases. You can set whitelist phases and the public mint.') }} <b>{{ __('You must have set at least one mint phase with a maximum of 3.') }}</b></p>
                        <p class="text-mintpad-300 font-regular text-sm mb-8">{{ __('When you only set one mint phase, this will be the date and time that people can mint your collection.') }}</p>

                        <div v-for="(phase, index) in claimPhases" class="w-full grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            <div class="col-span-3">
                                <x-gray-button href="#" class="float-right mt-2 !px-3 !py-2 text-lg" @click.prevent="deleteClaimPhase(index)"><i class="fas fa-trash-alt"></i></x-gray-button>
                                <h3 class="basis-full text-2xl mb-1" v-html="'Phase '+(index+1)"></h3>
                                <p class="text-mintpad-300 font-regular text-sm mb-1" v-html="claimPhaseInfo[index]"></p>
                            </div>
                            <div>
                                <x-label for="start" :value="__('Phase start time?')" />
                                <x-input id="start" class="mt-1 w-full" type="datetime-local" v-model="phase.startTime" required />
                            </div>
                            <div>
                                <x-label for="max-quantity" :value="__('Number of NFTs in this phase? (0 = unlimited)')" />
                                <x-input id="max-quantity" class="mt-1 w-full" type="number" v-model="phase.maxQuantity" required />
                            </div>
                            <div class="relative">
                                <x-label for="price" :value="__('Mint price')" />
                                <x-input id="price" class="mt-1 w-full" type="text" v-model="phase.price" required />
                                <label v-html="collection.token" class="absolute right-0 mr-5 mt-4 text-mintpad-500"></label>
                            </div>
                            <div>
                                <x-label for="max-quantity-wallet" :value="__('Claims per wallet')" />
                                <x-select class="mt-1 !w-full" v-model="phase.waitInSeconds" :options="['1 claim', 'Unlimited claims']"></x-select>
                            </div>
                            <div>
                                <x-label for="whitelist" :value="__('Enable whitelist')" class="mb-4" />
                                <x-radio v-bind:id="'whitelist-0-'+index" type="radio" v-model="phase.whitelist" value="0" class="inline-block" /><x-label v-bind:for="'whitelist-0-'+index" class="inline-block mr-2" :value="__('No')" />
                                <x-radio v-bind:id="'whitelist-1-'+index" type="radio" v-model="phase.whitelist" value="1" class="inline-block" /><x-label v-bind:for="'whitelist-1-'+index" class="inline-block" :value="__('Yes')" />
                            </div>
                            <div v-if="phase.whitelist == 1">
                                <x-label :value="__('Whitelist CSV file')" />
                                <p class="text-sm mt-1 text-mintpad-300"><x-gray-button href="#" class="!py-3" @click.prevent="toggleWhitelistModal(index, true)">Upload whitelist</x-gray-button><span class="ml-3" v-html="phase.snapshot.length"></span> addresses</p>
                            </div>

                            <div v-if="phase.modal" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                <div class="fixed z-10 inset-0 overflow-y-auto">
                                    <div class="flex items-end sm:items-center justify-center min-h-full p-4 sm:p-0">
                                        <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-5xl sm:w-full">
                                            <div class="bg-white p-14">
                                                <a href="#" class="absolute right-4 top-3 text-3xl text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="toggleWhitelistModal(index, false)"><i class="fas fa-times"></i></a>
                                                <div class="overflow-y-auto" v-bind:class="{'h-96': phase.snapshot != 0}">
                                                    <table v-if="phase.snapshot != 0" class="w-full font-medium">
                                                        <tr>
                                                            <th class="font-semibold">Address</th>
                                                            <th class="font-semibold">Max claimable</th>
                                                        </tr>
                                                        <tr v-for="wallet in phase.snapshot">
                                                            <td class="font-medium">@{{ wallet.address }}</td>
                                                            <td class="font-medium">@{{ phase.quantityLimitPerTransaction }}</td>
                                                        </tr>
                                                    </table>
                                                    <div v-else>
                                                        <h3 class="text-2xl mb-1">{{ __('Upload whitelist') }}</h3>
                                                        <p class="text-mintpad-300 font-regular text-sm">{{ __('Upload a .CSV file. One wallet address per row.') }}</b></p>
                            
                                                        <label class="block my-5 text-mintpad-300">
                                                            <span class="sr-only">Choose File</span>
                                                            <input type="file" @change="uploadWhitelist($event, index)" class="inline-block file:mr-2 file:px-4 file:py-3 file:bg-mintpad-200 file:text-mintpad-300 hover:text-mintpad-400 file:rounded-lg file:text-sm file:text-center file:border-0" name="whitelist_file" />
                                                        </label>

                                                    </div>
                                                </div>
                                                <div v-if="phase.snapshot != 0" class="w-full text-right mt-4">
                                                    <x-gray-button href="#" @click.prevent="resetWhitelist(index)">Reset list</x-gray-button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="claimPhases.length == 0">
                            <p class="">{{ __('No mint phases set yet.') }}</p>
                        </div>
                        <div class="w-full mt-10">
                            <x-default-button href="#" class="w-full !py-3" @click.prevent="addClaimPhase">Add mint phase</x-default-button>
                        </div>
                        <div class="w-full mt-10">
                            <span content="This action will trigger a transaction" v-tippy>
                                <x-button href="#" @click.prevent="updateClaimPhases"><i class="fas fa-cloud-upload-alt mr-2"></i> {{ __('Update mint phases') }}</x-button>
                            </span>
                        </div>
                    </form>
                </div>
                <div v-if="page.tab == 3">
                    <form method="POST" action="/" enctype="multipart/form-data">
                        @csrf

                        <div class="w-full">
                            <h3 class="text-2xl mb-4 mt-6">
                                {{ __('Add images to your collection') }}
                                <x-blue-button href="#" class="ml-6 align-middle !rounded-full !px-4 !py-1 !text-xs" @click.prevent="openYouTubeModal('https://www.youtube.com/embed/95tJuaWhE6g')"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle">{{ __('Watch tutorial') }}</span></x-blue-button>
                            </h3>
                            <p class="text-mintpad-300 font-regular text-sm">{{ __('Upload your NFT collection. If you have not yet generated your NFT collection, use our free NFT generator to generate your collection.') }}</b></p>
                            <p class="font-semibold text-sm mb-4"><x-link href="/examples/snapshot.csv">{{ __('Download a sample collection.') }}</x-link></p>

                            <label class="block mt-10 text-mintpad-300">
                                <span class="sr-only">Choose Files</span>
                                <input type="file" @change="uploadCollection" id="image_collection" class="inline-block file:mr-2 file:px-4 file:py-3 file:bg-mintpad-200 file:text-mintpad-300 hover:text-mintpad-400 file:rounded-lg file:text-sm file:text-center file:border-0" name="image_collection[]" accept="application/json image/jpeg, image/png, image/jpg, image/gif" directory webkitdirectory mozdirectory multiple/>
                            </label>
                            <p class="text-mintpad-300 font-regular text-sm mb-10 mt-2 ml-1">{{ __('Your upload must contain images and JSON files.') }}</p>

                            <div v-if="upload">
                                <p class="text-sm mb-2">We are uploading your collection, please don't close this page or the upload will fail!</p>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-300">
                                    <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-1000" v-bind:style="{width: upload.width+'%'}"></div>
                                </div>
                            </div>
                        </div>
                        <div v-if="collection.previews.length > 0">
                            <h3 class="text-2xl mb-4 mt-6">{{ __('Preview of your collection') }}</h3>
                            <div class="grid grid-cols-4">
                                <div v-for="preview in collection.previews">
                                    <div class="p-1 text-sm rounded-lg">
                                        <img class="w-full max-w-max transition-all duration-500 rounded-lg" :src="preview.image.src" />
                                    </div>
                                </div>
                            </div>
                            <div class="w-full mt-5">
                                <span content="This action will trigger a transaction" v-tippy>
                                    <x-button href="#" @click.prevent="updateCollection"><i class="fas fa-cloud-upload-alt mr-2"></i> {{ __('upload collection') }}</x-button>
                                </span>
                            </div>
                        </div>

                        <div class="text-sm">
                            <h3 class="text-2xl mb-4 mt-6">{{ __('Your collection') }}</h3>
                            <p v-if="collection.nfts.length == 0" class="text-mintpad-300 font-regular text-sm">{{ __('Your collection is still empty :(') }}</p>
                            <p v-else class="text-mintpad-300 text-sm">Total minted @{{ collection.totalRatio }}% (@{{ collection.totalClaimedSupply}}/@{{ collection.totalSupply }})</p>
                            <div class="grid grid-cols-4 mt-2">
                                <div class="p-1 text-center text-sm" v-for="nft in collection.nfts">
                                    <img class="w-full max-w-max transition-all duration-500 rounded-lg" :src="nft.metadata.image" />
                                </div>
                            </div> 
                        </div> 
                    </form>
                </div>
                <div v-if="page.tab == 4">
                    <h3 class="text-2xl mb-4 mt-6">
                        {{ __('Mint settings') }}
                        <x-blue-button href="#" class="ml-6 align-middle !rounded-full !px-4 !py-1 !text-xs" @click.prevent="openYouTubeModal('https://www.youtube.com/embed/95tJuaWhE6g')"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle">{{ __('Watch tutorial') }}</span></x-blue-button>
                    </h3>
                    <p class="text-mintpad-300 font-regular text-sm mb-4">{{ __('Here you can add some information about your collection that will be shown on your collections mint page.') }}</p>

                    <div class="w-full grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <div class="mb-4">
                                <x-label for="permalink" :value="__('Permalink')" />
                                <div class="grid grid-cols-2 gap-2">
                                    <span class="inline-block border-2 border-gray-300 text-sm bg-gray-200 rounded-lg p-3 mt-1">{{ config('app.mint_url') }}/</span>
                                    <x-input id="permalink" class="mt-1" type="text" v-model="collection.permalink" />
                                </div>
                            </div>
                            <div class="mb-4">
                                <x-label for="socials-website" :value="__('Website')" />
                                <x-input id="socials-website" class="mt-1 w-full" type="text" v-model="collection.website" placeholder="https://www.example.com" />
                            </div>
                            <div class="mb-4">
                                <x-label for="socials-roadmap" :value="__('Roadmap')" />
                                <x-input id="socials-roadmap" class="mt-1 w-full" type="text" v-model="collection.roadmap" placeholder="https://www.example.com/roadmap" />
                            </div>
                            <div class="mb-4">
                                <x-label for="socials-twitter" :value="__('Twitter')" />
                                <x-input id="socials-twitter" class="mt-1 w-full" type="text" v-model="collection.twitter" placeholder="https://www.example.com/twitter" />
                            </div>
                            <div>
                                <x-label for="socials-discord" :value="__('Discord')" />
                                <x-input id="socials-discord" class="mt-1 w-full" type="text" v-model="collection.discord" placeholder="https://www.example.com/discord" />
                            </div>
                        </div>
                        <div>
                            <x-label for="collection-about" :value="__('About the collection')" />
                            <x-textarea id="collection-about" class="mt-1 w-full" :rows="12" v-model="collection.about"></x-textarea>
                        </div>
                    </div>

                    <div class="w-full">
                        <x-link-button href="#" @click.prevent="updateMintSettings">{{ __('Update mint settings') }}</x-link-button>
                        <x-blue-button href="{{ route('mint.index', $collection->permalink) }}" target="_blank" class="ml-2">{{ __('View collection page') }}</x-blue-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
