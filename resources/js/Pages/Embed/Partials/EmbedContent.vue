<script setup>
import Box from '@/Components/Box.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import { checkCurrentBlockchain, getBlockchains } from '@/Helpers/Blockchain'
import { WeiToValue, calculateTransactionFee } from '@/Helpers/Helpers'
import { reportError } from '@/Helpers/Sentry'
import { getSmartContractFromSigner } from '@/Helpers/Thirdweb'
import { getMetaMaskError, switchChainTo } from '@/Wallets/MetaMask'
import { connectWallet, getDefaultWalletData, reconnectWallet } from '@/Wallets/Wallet'
import { ref, inject, onMounted, watch } from 'vue'

const props = defineProps({
    collection: Object,
    collectionData: Object,
    editMode: Boolean,
    validBlockchain: Boolean,
    currentToken: String
})

let validBlockchain = ref(false)
let wallet = ref(getDefaultWalletData())
let blockchains = ref(getBlockchains())
let mintAmount = ref(1)
let currentMintPhase = ref(0)
let buttonLoading = ref(false)
let messages = ref([])
const emitter = inject('emitter')

watch(() => props.collectionData.activeMintPhase, (newValue, oldValue) => {
    currentMintPhase.value = props.collectionData.activeMintPhase
})

onMounted(async () => {
    // Connect wallet
    wallet.value = await reconnectWallet()

    // Init app
    validBlockchain.value = checkCurrentBlockchain(blockchains, props.collection.chain_id, wallet)
})

const switchBlockchain = async () => {
    const status = await switchChainTo(props.chainId)
    if (status !== true) {
        emitter.emit('new-message', {type: 'error', message: status})
    }
}
const previousPhase = () => {
    const phaseCount = props.collectionData.claimPhases.length - 1
    if (currentMintPhase.value == 0) {
        currentMintPhase.value = phaseCount
    } else {
        currentMintPhase.value--
    }
}
const nextPhase = () => {
    const phaseCount = props.collectionData.claimPhases.length - 1
    if (currentMintPhase.value == phaseCount) {
        currentMintPhase.value = 0
    } else {
        currentMintPhase.value++
    }
}
const mintNFT = async () => {
    if (props.editMode) {
        alert('mint complete, nog aanpassen!')
    } else {
        if (props.collectionData.claimPhases.length == 0) {
            emitter.emit('new-message', {type: 'error', message: 'You cannot mint this NFT yet because no mint phases have been set yet'})
            return
        }

        buttonLoading.value = true
        try {
            // Set contract
            const contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
            if (props.collection.type == 'ERC721') {
                if (props.collectionData.contractType == 'DropERC721') {
                    await contract.claim(mintAmount.value)
                } else {
                    const preparedClaim = await contract.claim.prepare(mintAmount.value)
                    const overrideValue = preparedClaim.overrides.value == undefined ? 0 : WeiToValue(preparedClaim.overrides.value)
                    // let valueOverride = ((props.collectionData.transactionFee + overrideValue) * 1000000000000000000).toString()
                    // let valueOverride = ethers.utils.parseUnits((props.collectionData.transactionFee + overrideValue).toString(), 18)
                    preparedClaim.overrides.value = calculateTransactionFee(props.collectionData.transactionFee, overrideValue)
                    await preparedClaim.execute()
                }
            } else if (props.collection.type.startsWith('ERC1155')) {
                if (props.collectionData.contractType == 'DropERC1155') {
                    await contract.claim(0, mintAmount.value)
                } else {
                    const preparedClaim = await contract.claim.prepare(props.currentToken, mintAmount.value)
                    const overrideValue = preparedClaim.overrides.value == undefined ? 0 : WeiToValue(preparedClaim.overrides.value)
                    // let valueOverride = ((props.collectionData.transactionFee + overrideValue) * 1000000000000000000).toString()
                    // let valueOverride = ethers.utils.parseUnits((props.collectionData.transactionFee + overrideValue).toString(), 18)
                    preparedClaim.overrides.value = calculateTransactionFee(props.collectionData.transactionFee, overrideValue)
                    await preparedClaim.execute()
                }
            }

            messages.value.push({type: 'success', message: 'NFT minted!'})
        } catch (error) {
            console.log('error', error)
            
            let metamaskError = getMetaMaskError(error)
            if (metamaskError) {
                messages.value.push({type: 'error', message: metamaskError})
            } else {
                reportError(error)
                messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
            }
        }

        buttonLoading.value = false
    }
}

</script>
<template>
    <div id="embed-mint-box" class="sm:col-span-2" :class="{dark: collectionData.settings.darkmode}">
        <form>
            <Box class="mint-bg-box mb-0 mint-border-dark" title="Mint an NFT">
                <template v-slot:action>
                    <span class="inline-block absolute top-3 right-8 mint-text-dark" content="Check contract address" v-tippy><a :href="blockchains[collection.chain_id].explorers[0].url+'/address/'+collection.address" target="_blank" class="text-lg"><i class="fa-regular fa-file-contract"></i></a></span>
                </template>
                <div v-if="collectionData.settings.phases" class="w-full bg-mintpad-400/10 mint-bg-phase">
                    <div v-if="collectionData.claimPhases.length > 0" class="flex items-center">
                        <div class="py-6 pl-8">
                            <a href="#" @click.prevent="previousPhase" class="align-middle text-xl mint-text-dark"><i class="fa-solid fa-arrow-left"></i></a>
                        </div>
                        <div class="grow px-6 py-4">
                            <div v-for="(phase, index) in collectionData.claimPhases" class="relative min-h-[6.5rem]" :class="[currentMintPhase == index ? 'block' : 'hidden']">
                                <h3 class="mb-2 mint-text-dark">{{ phase.name }}</h3>
                                <span v-if="phase.active" class="inline-block absolute top-0 right-0 sm:w-auto mx-0 sm:mx-3 px-4 py-1 text-xs border mint-active-button rounded-full">Active</span>
                                <div>
                                    <p>
                                        <span v-if="phase.maxClaimableSupply === 0">NFTs: <span class="text-primary-600 mint-text-primary font-medium">unlimited</span></span>
                                        <span v-else>NFTs: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.maxClaimableSupply"></span></span>
                                        • Price: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.price"></span> <span class="text-primary-600 mint-text-primary font-medium" v-html="blockchains[collection.chain_id].nativeCurrency.symbol"></span>
                                        <span v-if="phase.maxClaimablePerWallet === 0"> • Max claims: <span class="text-primary-600 mint-text-primary font-medium">unlimited</span></span>
                                        <span v-else> • Max claims: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.maxClaimablePerWallet"></span></span>
                                        <span v-if="phase.whitelist"> • Whitelist: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.snapshot.length"></span></span>
                                        <span v-else> • Whitelist: <span class="text-primary-600 mint-text-primary font-medium">No</span></span>
                                    </p>
                                </div>
                                <div v-if="typeof collectionData.timers[index] === 'object' && collectionData.timers[index].state != undefined" class="mt-2 text-sm text-mintpad-700">
                                    <div class="relative w-full text-center font-regular">
                                        <p class="absolute left-0 top-2 font-medium mint-text-dark"><span v-html="collectionData.timers[index].state"></span> in</p>
                                        <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="collectionData.timers[index].days"></span>
                                        <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="collectionData.timers[index].hours"></span>
                                        <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="collectionData.timers[index].minutes"></span>
                                        <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="collectionData.timers[index].seconds"></span>
                                    </div>
                                </div>
                                <p v-else-if="typeof collectionData.timers[index] !== 'object' && collectionData.timers[index] !== Infinity" class="mt-6 text-sm text-mintpad-700 font-medium">Phase ended</p>
                            </div>
                        </div>
                        <div class="py-6 pr-8">
                            <a href="#" @click.prevent="nextPhase" class="align-middle text-xl mint-text-dark"><i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div v-else-if="collectionData.claimPhases.length == 0 && collectionData.loading == false" class="w-full px-6 py-4">
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
                    <p class="font-regular text-center mb-4">Start minting by clicking the button below</p>
                    <div v-if="!editMode" class="flex gap-2">
                        <Input type="number" v-model="mintAmount" min="1" :max="collectionData.maxMintAmount == 0 ? 99999 : collectionData.maxMintAmount" class="!mb-0 !w-28 mint-bg-phase" />                 
                        <Button v-if="!wallet.account" @click.prevent="connectWallet('metamask')" class="w-full mint-bg-primary">Connect MetaMask</Button>
                        <Button v-else-if="validBlockchain !== true" @click.prevent="switchBlockchain" class="w-full mint-bg-primary">Switch blockchain</Button>
                        <Button v-else="" @click.prevent="mintNFT" :disabled="collectionData.claimPhases.length == 0" class="w-full mint-bg-primary" :loading="buttonLoading">Start minting <span v-if="collectionData.activeMintPhase !== false">(<span v-if="collectionData.claimPhases[collectionData.activeMintPhase]" v-html="collectionData.claimPhases[collectionData.activeMintPhase].price"></span> <span v-html="blockchains[collection.chain_id].nativeCurrency.symbol"></span>)</span></Button>
                    </div>
                    <div v-else class="flex gap-2">
                        <Input type="number" value="1" class="!mb-0 !w-28 mint-bg-phase mint-text-dark" />   
                        <Button @click.prevent="mintNFT" class="w-full mint-bg-primary">Start minting (0.2 MATIC) </Button>
                    </div>
                    <div class="grid grid-cols-2 mt-4 text-sm font-medium">
                        <div>
                            <p>Total minted</p>
                        </div>
                        <div class="text-right">
                            <p v-if="collection.type.startsWith('ERC1155') && collectionData.totalSupply == 0">{{ collectionData.totalClaimedSupply }}</p>
                            <p v-else>{{ collectionData.totalRatioSupply }}% ({{ collectionData.totalClaimedSupply}}/{{ collectionData.totalSupply }})</p>
                        </div>
                    </div>
                    <div class="w-full mt-2 rounded-full bg-primary-300 mint-bg-primary-sm">
                        <div class="rounded-full bg-primary-600 mint-bg-primary p-1" :style="{width: collectionData.totalRatioSupply+'%'}"></div>
                    </div>
                </div>
            </Box>
        </form>
    </div>
</template>