<script setup>
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import BoxRow from '@/Components/BoxRow.vue'
import ButtonGray from '@/Components/Form/ButtonGray.vue'
import { getBlockchains } from '@/Helpers/Blockchain'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { shortenWalletAddress, copyToClipboard, parseClaimConditions } from '@/Helpers/Helpers'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, provide, onMounted, inject } from 'vue'
import axios from 'axios'
import { getDefaultWalletData, reconnectWallet } from '@/Wallets/Wallet'
import { switchChainTo } from '@/Wallets/MetaMask'
import Button from '@/Components/Form/Button.vue'
import Modal from '@/Components/Modal.vue'
import { getSmartContractFromSigner, getCollectionData } from '@/Helpers/Thirdweb'
import LinkBlue from '@/Components/LinkBlue.vue'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}

const props = defineProps({
    'collections': Object
})

const form = useForm({});

let loading = ref(true)
let wallet = ref(getDefaultWalletData())
let collectionData = ref({
    show: false,
    loading: true
})
let validBlockchain = ref(true)
let blockchains = ref(getBlockchains())
const emitter = inject('emitter')

provide('wallet', wallet)
provide('transaction', {show: false, message: ''})

onMounted(async () => {
    // Connect wallet
    wallet.value = await reconnectWallet()
    
    // Done loading
    loading.value = false
})

const deleteCollection = (collectionID) => {
    if (confirm("Are you sure you want to delete this collection?") == true) {
        form.delete(route('admin.collections.destroy', collectionID), {})
    }
}
const openCollectionModal = async (collectionID) => {
    collectionData.value.show = true

    const result = props.collections.find(({ id }) => id === collectionID)
    const contract = await getSmartContractFromSigner(wallet.value.signer, result.chain_id, result.address, result.type)
    try {
        const data = await getCollectionData(contract, result.type, false, 8)

        collectionData.value.chainId = result.chain_id

        // Settings
        collectionData.value.name = data.metadata.name
        collectionData.value.feeRecipient = data.royalties.feeRecipient
        collectionData.value.royalties = data.royalties.royalties

        // Fees
        collectionData.value.primarySalesRecipient = data.sales.primarySalesRecipient

        // Claim phases
        collectionData.value.claimPhases = parseClaimConditions(data.claimConditions)

        // Collection
        collectionData.value.totalSupply = data.totalSupply
        collectionData.value.totalClaimedSupply = data.totalClaimedSupply
        collectionData.value.totalRatioSupply = data.totalRatioSupply
        collectionData.value.nfts = data.nfts

        collectionData.value.loading = false
    } catch(error) {
        //
    }
}
const closeModal = () => {
    collectionData.value.show = false
    collectionData.value.loading = true
}
const switchBlockchain = async (chainId) => {
    const status = await switchChainTo(chainId)
    if (status !== true) {
        emitter.emit('new-message', {type: 'error', message: status})
    }
}
</script>
<template>
    <AuthenticatedLayout :loading="loading" :valid-blockchain="validBlockchain">
        <Head title="Collections" />

        <div class="text-center mb-10">
            <h1>Collections</h1>
        </div>
        
        <Box class="w-full mb-12" title="Collections">
            <template v-slot:action>
                <LinkBlue :href="route('admin.collections.create')" class="!absolute top-2.5 right-8 !py-1.5 !px-6" @click.prevent="">Add collection</LinkBlue>
            </template>
            <div v-if="collections">
                <BoxRow class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                    <div class="basis-1/2 sm:basis-4/12">Collection name</div>
                    <div class="basis-1/2 sm:basis-2/12">Type</div>
                    <div class="hidden sm:block basis-2/12">Blockchain</div>
                    <div class="basis-4/12"></div>
                </BoxRow>
                <BoxRow v-for="collection in collections" class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-full sm:basis-4/12 font-semibold"><Link class="hover:underline" :href="route('collections.edit', collection.id)">{{ collection.name }}</Link></div>
                    <div class="basis-full sm:basis-2/12 font-semibold">{{ collection.type }}</div>
                    <div class="basis-full sm:basis-2/12 font-semibold">{{ blockchains[collection.chain_id].name }} ({{ blockchains[collection.chain_id].nativeCurrency.symbol }})</div>
                    <div class="basis-full sm:basis-4/12 text-center sm:text-right">
                        <ButtonGray content="Copy contract address" @click="copyToClipboard" :text="collection.address" class="!text-sm !bg-mintpad-100 dark:!bg-mintpad-700 !px-3 !py-1 !font-mono" v-tippy><i class="fas fa-copy mr-2 text-mintpad-700 dark:text-white"></i>{{ shortenWalletAddress(collection.address) }}</ButtonGray> 
                        <LinkBlue element="a" :href="route('mint.index', collection.permalink)" target="_blank" class="ml-2 !px-2">Mint page</LinkBlue>
                        <span v-if="wallet.chainId != collection.chain_id" :content="'You need to switch to '+blockchains[collection.chain_id].nativeCurrency.symbol" v-tippy>
                            <ButtonGray href="#" @click.prevent="switchBlockchain(collection.chain_id)" class="ml-2 !px-2 w-24">Switch</ButtonGray>
                        </span>
                        <Button v-else href="#" @click.prevent="openCollectionModal(collection.id)" class="ml-2 !px-2 w-24">More info</Button>
                        <a href="#" @click.prevent="deleteCollection(collection.id)" class="ml-2 hover:text-red-700"><i class="fas fa-trash-alt"></i></a>
                    </div>
                </BoxRow>
            </div>
            <BoxContent v-else> 
                <p>You don't have any collections yet</p>
            </BoxContent>
        </Box>

        <Modal title="Whitelist addresses" :show="collectionData.show" @close="closeModal">
            <div v-if="collectionData.loading">Loading...</div>
            <div v-else>
                <p>Chain: <span class="text-mintpad-500 dark:text-white">{{ blockchains[collectionData.chainId].name }} ({{ blockchains[collectionData.chainId].nativeCurrency.symbol }}) with ID {{ collectionData.chainId }}</span></p>
                <p>Primary sales: <span class="text-mintpad-500 dark:text-white">{{ collectionData.primarySalesRecipient }}</span></p>
                <p>Royalties: <span class="text-mintpad-500 dark:text-white">{{ collectionData.royalties }}% to {{ collectionData.feeRecipient }}</span></p>
                <p>Claim phases: <span class="text-mintpad-500 dark:text-white">{{ collectionData.claimPhases.length }}</span></p>
                <p v-if="collectionData.nfts.length == 0">Total minted: <span class="text-mintpad-500 dark:text-white">NFT list empty</span></p>
                <p v-else>Total minted: <span class="text-mintpad-500 dark:text-white">{{ collectionData.totalRatioSupply }}% ({{ collectionData.totalClaimedSupply}}/{{ collectionData.totalSupply }})</span></p>
                <div class="grid grid-cols-4 mt-2">
                    <div class="p-1 text-center text-sm" v-for="nft in collectionData.nfts">
                        <img class="w-full max-w-max transition-all duration-500 rounded-md" :src="nft.metadata.image" />
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>