<script setup>
import Box from '@/Components/Box.vue'
import Button from '@/Components/Form/Button.vue'
import { checkCurrentBlockchain, getBlockchains } from '@/Helpers/Blockchain'
import { calculateTransactionFee } from '@/Helpers/Helpers'
import { resportError } from '@/Helpers/Sentry'
import { getSmartContractFromSigner } from '@/Helpers/Thirdweb'
import { connectMetaMask, getMetaMaskError, switchChainTo } from '@/Wallets/MetaMask'
import { reconnectWallet } from '@/Wallets/Wallet'
import { ref, inject, onMounted } from 'vue'

const props = defineProps({
    collection: Object,
    collectionData: Object,
    editMode: Boolean,
    validBlockchain: Boolean
})

let validBlockchain = ref(false)
let wallet = ref({account: false})
let blockchains = ref(getBlockchains())
let buttonLoading = ref(false)
let messages = ref([])
const emitter = inject('emitter')

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
const burnNFTs = async (e) => {
    buttonLoading.value = true
    try {
        const contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
        const firstClaimPhase = await contract.call('getClaimConditionById', [0, 0], {})
        // let valueOverride = (props.collectionData.transactionFee * 1000000000000000000).toString()
        await contract.call('evolve', [wallet.value.account, firstClaimPhase.currency], {
            value: calculateTransactionFee(props.collectionData.transactionFee, 0.0)
        })
    } catch (error) {
        console.log('error burn', error)
        let metamaskError = getMetaMaskError(error)
        if (metamaskError) {
            messages.value.push({type: 'error', message: metamaskError})
        } else {
            resportError(error)
            messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
        }
    }

    buttonLoading.value = false
}
</script>
<template>
    <div id="embed-mint-box" class="sm:col-span-2" :class="{dark: collectionData.settings.darkmode}">
        <form>
            <Box class="mint-bg-box mb-0 mint-border-dark" title="Burn your NFTs">
                <template v-slot:action>
                    <span class="inline-block absolute top-3 right-8 mint-text-dark" content="Check contract address" v-tippy><a :href="blockchains[collection.chain_id].explorers[0].url+'/address/'+collection.address" target="_blank" class="text-lg"><i class="fa-regular fa-file-contract"></i></a></span>
                </template>

                <div class="p-6">
                    <p class="font-regular text-center mb-4">You can evolve your NFTs by burning <b>{{ collectionData.nftsToBurn }}</b> NFTs.</p>
                    <div class="flex gap-2">
                        <Button v-if="!wallet.account" @click.prevent="connectMetaMask" class="w-full mint-bg-primary">Connect MetaMask</Button>
                        <Button v-else-if="validBlockchain !== true" @click.prevent="switchBlockchain" class="w-full mint-bg-primary">Switch blockchain</Button>
                        <Button v-else @click.prevent="burnNFTs" :loading="collectionData.loading" class="w-full mint-bg-primary">Burn <b>{{ collectionData.nftsToBurn }}</b> NFTs</Button>
                    </div>
                </div>
            </Box>
        </form>
    </div>
</template>