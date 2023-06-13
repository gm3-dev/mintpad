<script setup>
import MinimalLayout from '@/Layouts/MinimalLayout.vue'
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { WeiToValue, setStyling } from '@/Helpers/Helpers'
import { getCollectionData, getSmartContract, getSmartContractFromSigner } from '@/Helpers/Thirdweb'
import { reconnectWallet } from '@/Wallets/Wallet'
import EmbedBurnContent from '../Embed/Partials/EmbedBurnContent.vue'
import { ethers } from 'ethers'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}
document.documentElement.classList.remove('dark')

const props = defineProps({
    collection: Object
})
let wallet = ref(false)
let editMode = ref(false)
let loading = ref(true)
let validBlockchain = ref(false)
let collectionData = ref({
    loading: true,
    id: props.collection.id,
    theme: {
        primary: {r: 0, g: 119, b: 255, a: 1},
        background: {r: 255, g: 255, b: 255, a: 1},
        phases: {r: 241, g: 243, b: 244, a: 1}
    },
    settings: {},
    totalSupply: 0,
    totalClaimedSupply: 0,
    totalRatioSupply: 0,
    nftsToBurn: 0,
    balance: {tier1: '...', tier2: '...'},
    transactionFee: 0,
})

onMounted(async() => {
    // Connect wallet
    wallet.value = await reconnectWallet()
    
    axios.get('/'+props.collection.id+'/fetch').then(async (response) => {
        // Set theme for mint
        if (response.data.theme.embed) {
            collectionData.value.theme = response.data.theme.embed
        }

        // Set settings
        if (response.data.settings.embed) {
            collectionData.value.settings = response.data.settings.embed
        }

        setStyling(collectionData.value)
        
        // Loading done
        loading.value = false

        // Set contract
        let contract
        if (wallet.account && validBlockchain === true) {
            contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
        } else {
            contract = await getSmartContract(props.collection.chain_id, props.collection.address, props.collection.type)
        }
        try {
            const data = await getCollectionData(contract, props.collection.type, true, false)
            const contractType = await contract.call('contractType')
            
            // Collection
            collectionData.value.totalSupply = data.totalSupply
            collectionData.value.totalClaimedSupply = data.totalClaimedSupply
            collectionData.value.totalRatioSupply = data.totalRatioSupply
            collectionData.value.nfts = data.nfts

            // Settings
            collectionData.value.contractType = ethers.utils.parseBytes32String(contractType)
            if (collectionData.value.contractType == 'DropERC721' || collectionData.value.contractType == 'DropERC1155') {
                collectionData.value.transactionFee = 0
            } else {
                let transactionFee = await contract.call('getTransactionFee')
                collectionData.value.transactionFee = WeiToValue(transactionFee.toString())
            }

            // Collection supply
            collectionData.value.nftsToBurn = data.nftsToBurn

            // Stop loading
            collectionData.value.loading = false
        } catch(error) {
            console.log('error', error)
            //
        }
    })
})
</script>
<template>
    <MinimalLayout :loading="loading" :overlay="loading" :valid-blockchain="validBlockchain" :chain-id="collection.chain_id">
        <EmbedBurnContent :edit-mode="editMode" :collection="collection" :collection-data="collectionData" />
    </MinimalLayout>
</template>