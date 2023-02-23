<div id="embed-mint-box" class="sm:col-span-2">
    <x-box class="mint-bg-box mb-0 mint-border-dark">
        <x-slot name="title" class="mint-border-dark"><span class="mint-text-dark">Mint an NFT</span></x-slot>
        <x-slot name="action"><span class="inline-block absolute top-3 right-8 mint-text-dark" content="Check contract address" v-tippy><a href="{{ $contract_url }}" target="_blank" class="text-lg"><i class="fa-regular fa-file-contract"></i></a></span></x-slot>
        <div>
            <div v-if="settings.phases" class="w-full bg-mintpad-400/10 mint-bg-phase">
                <div v-if="claimPhases.length > 0" class="flex items-center">
                    <div class="py-6 pl-8">
                        <a href="#" @click.prevent="previousPhase" class="align-middle text-xl mint-text-dark"><i class="fa-solid fa-arrow-left"></i></a>
                    </div>
                    <div class="grow px-6 py-4">
                        <div v-for="(phase, index) in claimPhases" class="relative min-h-[6.5rem]" v-bind:class="[activeMintPhase == index ? 'block' : 'hidden']">
                            <h3 class="mb-2 mint-text-dark">@{{ phase.name }}</h3>
                            <span v-if="phase.active" class="inline-block absolute top-0 right-0 sm:w-auto mx-0 sm:mx-3 px-4 py-1 text-xs border mint-active-button rounded-full">Active</span>
                            <div>
                                <p>NFTs: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.maxClaimableSupply"></span> 
                                • Price: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.price"></span> <span class="text-primary-600 mint-text-primary font-medium" v-html="collection.token"></span> 
                                <span v-if="phase.maxClaimablePerWallet !== 0">• Max claims: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.maxClaimablePerWallet"></span> </span>
                                <span v-if="phase.maxClaimablePerWallet == 0">• Max claims: <span class="text-primary-600 mint-text-primary font-medium">unlimited</span> </span>
                                <span v-if="phase.whitelist">• Whitelist: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.snapshot.length"></span></span></p>
                            </div>
                            <div v-if="typeof timers[index] === 'object' && timers[index].state != undefined" class="mt-2 text-sm text-mintpad-700">
                                <div class="relative w-full text-center font-regular">
                                    <p class="absolute left-0 top-2 font-medium mint-text-dark"><span v-html="timers[index].state"></span> in</p>
                                    <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="timers[index].days"></span>
                                    <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="timers[index].hours"></span>
                                    <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="timers[index].minutes"></span>
                                    <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="timers[index].seconds"></span>
                                </div>
                            </div>
                            <p v-else-if="timers[index] !== Infinity && typeof timers[index] !== 'object'" class="mt-6 text-sm text-mintpad-700 font-medium">{{ __('Phase ended') }}</p>
                        </div>
                    </div>
                    <div class="py-6 pr-8">
                        <a href="#" @click.prevent="nextPhase" class="align-middle text-xl mint-text-dark"><i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <div v-else-if="claimPhases.length == 0 && loadComplete" class="w-full px-6 py-4">
                    <div class="w-full min-h-[6.5rem]">
                        <p class="text-center">Minting is disabled because no mint phases are active.</p>
                    </div>
                </div>
                <div v-else class="w-full px-6 py-4">
                    <div class="w-full min-h-[6.5rem]">
                        <div class="bg-primary-300 mint-bg-primary-sm rounded-md w-1/2 h-5 mb-4 animate-pulse"></div>
                        <div class="bg-primary-300 mint-bg-primary-sm rounded-md w-full h-5 mb-4 animate-pulse"></div>
                        <div class="bg-primary-300 mint-bg-primary-sm rounded-md w-2/3 h-5 animate-pulse"></div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <p class="font-regular text-center mb-4">{{ __('Start minting by clicking the button below') }}</p>
                <div v-if="!editMode" class="flex gap-2">
                    <x-input type="number" v-model="mintAmount" min="1" v-bind:max="maxMintAmount" width="w-28" class="!mb-0 mint-bg-phase" />                 
                    <x-button v-if="!wallet.account" @click.prevent="connectMetaMask" class="w-full mint-bg-primary">Connect MetaMask</x-button>
                    <x-button v-else-if="hasValidChain !== true" @click.prevent="switchBlockchainTo(false)" class="w-full mint-bg-primary">Switch blockchain</x-button>
                    <x-button v-else="" @click.prevent="mintNFT" v-bind:disabled="claimPhases.length == 0" class="w-full mint-bg-primary">Start minting <span v-if="activeMintPhase !== false">(<span v-html="claimPhases[activeMintPhase].price"></span> <span v-html="collection.token"></span>)</span></x-button>
                </div>
                <div v-else class="flex gap-2">
                    <x-input type="number" value="1" width="w-28" class="!mb-0 mint-bg-phase mint-text-dark" />   
                    <x-button @click.prevent="mintNFT" class="w-full mint-bg-primary">Start minting (0.2 MATIC) </x-button>
                </div>
                <div class="grid grid-cols-2 mt-4 text-sm font-medium">
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
            </div>
        </div>
    </x-box>
</div>