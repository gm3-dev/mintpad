<x-app-layout>
    <div class="max-w-7xl mx-auto px-6">
        <div class="relative">
            <input type="hidden" id="collectionID" name="collectionID" :value="{{ $collection->id }}" />

            <div v-if="!wallet.account"></div>
            <div v-else>
                <div class="text-center mb-10">
                    <h1>{{ __('Manage NFT collection') }}</h1>
                    <p>{{ __('You can adjust the settings of your collection here.') }}</p>
                </div>

                <div v-if="hasValidChain === true">
                    <div class="w-full grid grid-cols-2 gap-4 sm:block mb-8 text-left sm:text-center">
                        <status-button @click.prevent.native="changeEditTab(1)" :label="'Settings'" :complete="tabs.settings"></status-button>
                        <status-button @click.prevent.native="changeEditTab(2)" :label="'Mint phases'" :complete="tabs.phases"></status-button>
                        <status-button @click.prevent.native="changeEditTab(3)" :label="'Upload collection'" :complete="tabs.collection"></status-button>
                        <status-button @click.prevent.native="changeEditTab(4)" :label="'Mint settings'" :complete="tabs.mint"></status-button>
                    </div>
                    <div class="w-full mb-6 text-center">
                        <x-gray-button href="#" @click.prevent="previousEditTab" v-bind:class="{'!text-mintpad-400': page.tab == 1}">Previous step</x-gray-button>
                        <h2 v-if="page.tab == 1" class="hidden sm:inline-block text-2xl w-1/4">Settings</h2>
                        <h2 v-if="page.tab == 2" class="hidden sm:inline-block text-2xl w-1/4">Mint phases</h2>
                        <h2 v-if="page.tab == 3" class="hidden sm:inline-block text-2xl w-1/4">Upload collection</h2>
                        <h2 v-if="page.tab == 4" class="hidden sm:inline-block text-2xl w-1/4">Mint settings</h2>
                        <x-gray-button href="#" @click.prevent="nextEditTab" v-bind:class="{'!text-mintpad-400': page.tab == 4}">Next step</x-gray-button>
                    </div>
                    <div v-show="page.tab == 1">
                        <form method="POST" action="{{ route('collections.update', $collection->id) }}" enctype="multipart/form-data">
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <x-box class="flex-1 mb-4">
                                        <x-slot name="title">General Settings</x-slot>
                                        <x-slot name="content">
                                            <div class="w-full flex flex-wrap">
                                                <div class="basis-full">
                                                    <x-label for="name" :value="__('Collection name')" class="relative" info="This is the name of your NFT collection." />
                                                    <x-input id="name" class="w-full" type="text" name="name" v-model="collection.name" autofocus />
                                                </div>
                                                <div class="basis-full">
                                                    <x-label for="description" :value="__('Collection description')" info="This should be a short description of your collection. This is displayed on marketplaces where people can trade your NFT." />
                                                    <x-textarea id="description" class="w-full" name="description" v-model="collection.description"></x-textarea>
                                                </div>
                                            </div>
                                        </x-slot>
                                    </x-box>
                                    <div class="w-full">
                                        <span content="This action will trigger a transaction" v-tippy>
                                            <x-button href="#" @click.prevent="updateMetadata" v-bind:disabled="buttons.settings">{{ __('Update settings') }}</x-button>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <x-box class="flex-1 mb-4">
                                        <x-slot name="title">Royalties</x-slot>
                                        <x-slot name="content">
                                            <div>
                                                <x-label for="fee_recipient" :value="__('Recipient address')" class="relative" info="This is the wallet address where the proceeds of your NFT collection go. By default, this is the wallet address that puts the NFT collection on the blockchain. Double check this address." />
                                                <x-input id="fee_recipient" class="w-full" type="text" name="fee_recipient" v-model="collection.fee_recipient" />
                                            </div>
                                            <div class="w-1/2">
                                                <x-label for="royalties" :value="__('Creator royalties (%)')" class="relative" info="This is how much percent you want to receive from secondary sales on marketplaces such as OpenSea and Magic Eden." />
                                                <x-input id="royalties" addon="%" class="w-full" step=".01" type="number" name="royalties" v-model="collection.royalties" />
                                            </div>
                                        </x-slot>
                                    </x-box>
                                    <div class="w-full">
                                        <span content="This action will trigger a transaction" v-tippy>
                                            <x-button href="#" @click.prevent="updateRoyalties" v-bind:disabled="buttons.royalties">{{ __('Update royalties') }}</x-button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div v-show="page.tab == 2">
                        <form method="POST" action="{{ route('collections.update', $collection->id) }}" enctype="multipart/form-data">
                            @method('PUT')

                            <x-box class="mb-4">
                                <x-slot name="title">Mint phases</x-slot>
                                <x-slot name="content">
                                    <p>{{ __('On this page you can set mint phases. You can set whitelist phases and the public mint.') }} <b>{{ __('You must have set at least one mint phase with a maximum of 3.') }}</b></p>
                                    <p>{{ __('When you only set one mint phase, this will be the date and time that people can mint your collection.') }}</p>
                                </x-slot>
                            </x-box>

                            <x-box v-for="(phase, index) in claimPhases" class="mb-4">
                                <x-slot name="title">@{{ 'Phase '+(index+1) }}</x-slot>
                                <x-slot name="action"><a href="#" class="absolute right-8 top-3 text-xs font-medium text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="deleteClaimPhase(index)">Delete phase</a></x-slot>
                                <x-slot name="content">
                                    <div class="w-full grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4">
                                        <div>
                                            <x-label for="start" :value="__('Phase start time')" info="This is the time and date when people can mint your NFT collection. Note: This time is shown in your local time." />
                                            <x-input id="start" type="datetime-local" v-model="phase.startTime" />
                                        </div>
                                        <div>
                                            <x-label for="max-quantity" :value="__('Number of NFTs')" info="The number of NFTs that will be released in this mint phase. (0 = unlimited)." />
                                            <x-input id="max-quantity" type="number" v-model="phase.maxClaimableSupply" />
                                        </div>
                                        <div class="relative">
                                            <x-label for="price" :value="__('Mint price')" info="The mint price people pay for one NFT from your collection." />
                                            <x-input id="price" addon="{{ $collection->token }}" step="0.001" type="number" v-model="phase.price" />
                                        </div>
                                        <div>
                                            <x-label for="max-quantity-wallet" :value="__('Claims per wallet')" info="Here you can choose whether people can only mint one NFT per wallet or unlimited." />
                                            <x-select class="!w-full" v-model="phase.maxClaimablePerWallet" :options="['Unlimited claims', '1 claim']"></x-select>
                                        </div>
                                        <div>
                                            <x-label for="phase-name" :value="__('Phase name')" info="Here you can give this mint phase a name. This is only visible on your own mint page." />
                                            <x-input id="phase-name" type="text" v-model="phase.name" />
                                        </div>
                                        <div>
                                            <x-label for="whitelist" :value="__('Enable whitelist')" info="Here you can choose whether to enable a whitelist or not." />
                                            <x-radio-group>
                                                <x-radio v-bind:id="'whitelist-0-'+index" type="radio" v-model="phase.whitelist" value="0" class="inline-block" /><x-label v-bind:for="'whitelist-0-'+index" class="inline-block mr-2" :value="__('No')" />
                                                <x-radio v-bind:id="'whitelist-1-'+index" type="radio" v-model="phase.whitelist" value="1" class="inline-block" /><x-label v-bind:for="'whitelist-1-'+index" class="inline-block" :value="__('Yes')" />
                                            </x-radio-group>
                                        </div>
                                        <div v-if="phase.whitelist == 1" class="col-span-2">
                                            <x-label :value="__('Whitelist CSV file')" info="Here you can upload a .CSV file with all whitelisted wallets." />
                                            <p class="text-sm"><x-gray-button href="#" @click.prevent="toggleWhitelistModal(index, true)">Upload CSV</x-gray-button><span class="ml-3" v-html="phase.snapshot.length"></span> addresses</p>
                                        </div>

                                        <div v-if="phase.modal">
                                            <x-modal title="Whitelist addresses">
                                                <x-slot name="close">
                                                    <a href="#" class="absolute right-4 top-3 text-xs font-medium text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="toggleWhitelistModal(index, false)">close</a>
                                                </x-slot>
                                                <div class="overflow-y-auto" v-bind:class="{'h-96': phase.snapshot != 0}">
                                                    <div v-if="phase.snapshot != 0" class="p-4 bg-primary-100 rounded-md border border-primary-200">
                                                        <p v-for="wallet in phase.snapshot">@{{ wallet.address }}</p>
                                                    </div>
                                                    <div v-else>
                                                        <p>Here you can upload a .CSV file with all whitelisted wallets. Not sure what your .CSV should contain?</p>
                                                        <p class="mb-4"><x-link href="/examples/snapshot.csv">{{ __('Download a demo whitelist.') }}</x-link></p>
                                                        <label class="block mb-4 text-mintpad-300">
                                                            <span class="sr-only">Choose File</span>
                                                            <x-input-file @change="uploadWhitelist($event, index)" name="whitelist_file" />
                                                        </label>
                                                        <p>{{ __('Upload a .CSV file. One wallet address per row.') }}</p>
                                                    </div>
                                                </div>
                                                <div v-if="phase.snapshot != 0" class="w-full mt-4">
                                                    <x-gray-button href="#" @click.prevent="resetWhitelist(index)">Delete</x-gray-button>
                                                    <x-button href="#" @click.prevent="toggleWhitelistModal(index, false)" class="ml-2">Save</x-button>
                                                </div>
                                            </x-modal>
                                        </div>
                                    </div>
                                </x-slot>
                            </x-box>

                            <x-box v-if="claimPhases.length == 0" class="mb-4">
                                <x-slot name="content">
                                    <p class="">{{ __('You have no mint phases set yet.') }}</p>
                                </x-slot>
                            </x-box>

                            <div class="w-full text-center mb-4">
                                <x-default-button href="#" @click.prevent="addClaimPhase"><i class="fa-solid fa-plus mr-2 text-lg align-middle"></i> Add another mint phase</x-default-button>
                            </div>
                            <div class="w-full">
                                <span content="This action will trigger a transaction" v-tippy>
                                    <x-button href="#" @click.prevent="updateClaimPhases" v-bind:disabled="buttons.phases">{{ __('Update mint phases') }}</x-button>
                                </span>
                            </div>
                        </form>
                    </div>
                    <div v-show="page.tab == 3">
                        <form method="POST" action="/" enctype="multipart/form-data">
                            @csrf

                            <div class="w-full">
                                <x-box class="mb-4">
                                    <x-slot name="title">Upload collection</x-slot>
                                    <x-slot name="content">
                                        <p>{{ __('Upload your NFT collection. If you have not yet generated your NFT collection, use our free') }} <x-link class="text-sm" href="{{ config('app.generator_url') }}" target="_blank">NFT generator</x-link> {{ __('to generate your collection.') }}</p>
                                        <p class="mb-4"><x-link href="/examples/demo-collection.zip">{{ __('Download a demo collection.') }}</x-link></p>
    
                                        <label class="block text-mintpad-300 mb-4">
                                            <span class="sr-only">Choose Files</span>
                                            <x-input-file @change="uploadCollection" id="image_collection" name="image_collection[]" accept="application/json image/jpeg, image/png, image/jpg, image/gif" directory webkitdirectory mozdirectory multiple/>
                                        </label>
                                        <p>{{ __('Your upload must contain images and JSON files.') }}</p>

                                        <div v-if="upload">
                                            <p class="text-sm mb-2">We are uploading your collection, please don't close this page or the upload will fail!</p>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-300">
                                                <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-1000" v-bind:style="{width: upload.width+'%'}"></div>
                                            </div>
                                        </div>
                                    </x-slot>
                                </x-box>
                            </div>

                            <x-box v-if="collection.previews.length > 0" class="mb-4">
                                <x-slot name="title">Preview of your collection</x-slot>
                                <x-slot name="content">
                                    <div class="grid grid-cols-4">
                                        <div v-for="preview in collection.previews">
                                            <div class="p-1 text-sm rounded-md">
                                                <img class="w-full max-w-max transition-all duration-500 rounded-md" :src="preview.image.src" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full mt-5">
                                        <p class="font-regular text-sm mb-4">{{ __('Uploading the images and JSON files can take a while. Do not close this page, and wait until you get a popup from your wallet.') }}</p>
                                        <span content="This action will trigger a transaction" v-tippy>
                                            <x-button href="#" @click.prevent="updateCollection">{{ __('Upload collection') }}</x-button>
                                        </span>
                                    </div>
                                </x-slot>
                            </x-box>

                            <x-box class="mb-4">
                                <x-slot name="title">Your collection</x-slot>
                                <x-slot name="content">
                                    <div class="text-sm">
                                        <p v-if="collection.nfts.length == 0">{{ __('Your collection is still empty.') }}</p>
                                        <p v-else>Total minted @{{ collection.totalRatio }}% (@{{ collection.totalClaimedSupply}}/@{{ collection.totalSupply }})</p>
                                        <div class="grid grid-cols-4 mt-2">
                                            <div class="p-1 text-center text-sm" v-for="nft in collection.nfts">
                                                <img class="w-full max-w-max transition-all duration-500 rounded-md" :src="nft.metadata.image" />
                                            </div>
                                        </div> 
                                    </div> 
                                </x-slot>
                            </x-box>
                        </form>
                    </div>
                    <div v-show="page.tab == 4">
                        <x-box class="mb-4">
                            <x-slot name="title">Mint settings</x-slot>
                            <x-slot name="content">
                                <p>{{ __('Here you can customize your mint page. Add SEO to your page and customize the design.') }}</p>
                            </x-slot>
                        </x-box>

                        <x-box class="mb-4">
                            <x-slot name="title">Permalink</x-slot>
                            <x-slot name="content">
                                <x-label for="permalink" :value="__('Permalink')" />
                                <x-input id="permalink" class="basis-1/3" position="left" :addon="config('app.mint_url').'/'" type="text" v-model="collection.permalink" />
                                <x-blue-button v-bind:href="collection.fullEditorUrl" target="_blank" class="mr-2">{{ __('Page editor') }}</x-blue-button>
                                <x-blue-button v-bind:href="collection.fullMintUrl" target="_blank">{{ __('View collection page') }}</x-blue-button>
                            </x-slot>
                        </x-box>

                        <x-box class="mb-4">
                            <x-slot name="title">SEO settings</x-slot>
                            <x-slot name="content">
                                <div class="w-full grid grid-cols-2 gap-4">
                                    <div>
                                        <div>
                                            <x-label for="seo-title" :value="__('Title')" info="This is the title that is displayed on search engine result pages, browser tabs, and social media. You can use up to 60 characters." />
                                            <x-input id="seo-title" class="w-full" type="text" v-model="collection.seo.title" />
                                        </div>

                                        <div>
                                            <x-label for="seo-description" :value="__('Description')" info="This is the description that is displayed on search engine result pages, browser tabs, and social media. You can use up to 155 characters." />
                                            <x-textarea id="seo-description" class="w-full" v-model="collection.seo.description"></x-textarea>
                                        </div>
                                    </div>
                                    <div>
                                        <x-label for="seo-image" :value="__('Social share image')" info="This is the thumbnail people see when you share your mintpage link. Thumbnail image size is 1280x720px." />

                                        <div v-if="collection.seo.image" class="relative">
                                            <x-gray-button href="#" class="absolute top-0 right-0 m-2 !px-3 !py-2 text-lg" @click.prevent="deleteSocialImage"><i class="fas fa-trash-alt"></i></x-gray-button>
                                            <img v-bind:src="collection.seo.image" class="rounded-md" />
                                        </div>
                                        <div v-else class="mb-4">
                                            <p v-if="loadingResource('social-sharing')" class="mt-1"><i class="fa-solid fa-cloud-arrow-up animate-bounce mr-2 text-lg"></i> uploading...</p>
                                            <x-input-file v-else id="upload-logo" @dragenter="dragEnterUploadResource('social-sharing')" @dragleave="dragLeaveUploadResource('social-sharing')" @change="addSocialImage" v-bind:class="resources['social-sharing'] ? resources['social-sharing'].classes : []" accept="image/jpeg, image/png, image/jpg" />
                                        </div>
                                    </div>
                                </div>
                            </x-slot>
                        </x-box>

                        <div class="w-full">
                            <x-button @click.prevent="updateMintSettings" v-bind:disabled="loadingResource('social-sharing')" v-bind:disabled="buttons.mint">{{ __('Update mint settings') }}</x-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
