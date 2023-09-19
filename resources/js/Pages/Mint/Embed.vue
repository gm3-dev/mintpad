<script setup>
import MinimalLayout from '@/Layouts/MinimalLayout.vue'
import { ref, onMounted } from 'vue'
import EmbedContent from '../Embed/Partials/EmbedContent.vue'
import axios from 'axios'
import { WeiToValue, getDoubleDigitNumber, parseClaimConditions, setStyling } from '@/Helpers/Helpers'
import { getCollectionData, getSmartContract, getSmartContractFromSigner } from '@/Helpers/Thirdweb'
import { getDefaultWalletData, reconnectWallet } from '@/Wallets/Wallet'
import { ethers } from 'ethers'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}
document.documentElement.classList.remove('dark')

const props = defineProps({
    collection: Object,
    token: String
})
let wallet = ref(getDefaultWalletData())
let editMode = ref(false)
let loading = ref(true)
let validBlockchain = ref(false)
let collectionData = ref({
    contractType: 'ERC721',
    loading: true,
    id: props.collection.id,
    theme: {
        primary: {r: 0, g: 119, b: 255, a: 1},
        background: {r: 255, g: 255, b: 255, a: 1},
        phases: {r: 241, g: 243, b: 244, a: 1}
    },
    settings: {},
    claimPhases: [],
    timers: {},
    activeMintPhase: 1,
    maxMintAmount: 1,
    mintAmount: 1,
    totalSupply: 0,
    totalClaimedSupply: 0,
    totalRatioSupply: 0,
    nfts: [],
    transactionFee: 0,
})
let currentNFT = ref(props.token)

onMounted(async() => {
    // Connect wallet
    wallet.value = await reconnectWallet()
    
    axios.get('/collection/'+props.collection.id+'/fetch').then(async (response) => {
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
        if (wallet.value.account && validBlockchain.value === true) {
            contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
        } else {
            contract = await getSmartContract(props.collection.chain_id, props.collection.address, props.collection.type)
        }
        try {
            const data = await getCollectionData(contract, props.collection.type, true, false, currentNFT.value)
            const contractType = await contract.call('contractType')
            
            collectionData.value.claimPhases = parseClaimConditions(data.claimConditions)
            setActiveClaimPhase()
            setClaimPhaseCounters()

            // Collection
            collectionData.value.totalSupply = data.totalSupply
            collectionData.value.totalClaimedSupply = data.totalClaimedSupply
            collectionData.value.totalRatioSupply = data.totalRatioSupply
            collectionData.value.nfts = data.nfts
            if (collectionData.value.contractType == 'DropERC721' || collectionData.value.contractType == 'DropERC1155') {
                collectionData.value.transactionFee = 0
            } else {
                let transactionFee = await contract.call('getTransactionFee')
                collectionData.value.transactionFee = WeiToValue(transactionFee.toString())
            }

            // Settings
            collectionData.value.contractType = ethers.utils.parseBytes32String(contractType)

            // Stop loading
            collectionData.loading = false
        } catch(error) {
            console.log('error', error)
            //
        }
    })
})

const setActiveClaimPhase = () => {
    for (var i = 0; i < collectionData.value.claimPhases.length; i++) {
        var claimPhase = collectionData.value.claimPhases[i]
        var from = new Date(claimPhase.startTime).getTime()
        var to = new Date(claimPhase.endTime).getTime()
        var now = new Date().getTime()
        if (now <= to && now >= from) {
            collectionData.value.claimPhases[i].active = true
            collectionData.value.maxMintAmount = claimPhase.maxClaimablePerWallet
            collectionData.value.activeMintPhase = i
        } else if (now >= from && to == 0) {
            collectionData.value.claimPhases[i].active = true
            collectionData.value.maxMintAmount = claimPhase.maxClaimablePerWallet
            collectionData.value.activeMintPhase = i
        } else {
            collectionData.value.claimPhases[i].active = false
        }
    }
}

const setClaimPhaseCounters = () => {
    for (var i = 0; i < collectionData.value.claimPhases.length; i++) {
        collectionData.value.timers[i] = {}
        setCountDown(i)
    }
}

const setCountDown = (i) => {
    var claimPhase = collectionData.value.claimPhases[i]
    var countDownDate = new Date(claimPhase.startTime).getTime()
    var endDate = new Date(claimPhase.endTime).getTime()
    var state = 'Starts'

    var now = new Date().getTime()
    var distance = endDate - now
    if (distance < 0 && endDate != claimPhase.endTime) {
        collectionData.value.timers[i] = false
    } else {
        var x = setInterval(() => {
            var now = new Date().getTime()

            var distance = countDownDate - now
            if (distance < 0) {
                if (endDate) {
                    state = 'Ends'
                }
                countDownDate = endDate
                var distance = countDownDate - now
            }

            // Last phase with no end date
            if (endDate === 0 && distance < 0) {
                clearInterval(x)
                collectionData.value.timers[i] = Infinity
            // Past phases
            } else if (distance < 0) {
                clearInterval(x)
                collectionData.value.timers[i] = false
            
            // Coming or runing phases
            } else {
                var days = Math.floor(distance / (1000 * 60 * 60 * 24))
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
                var seconds = Math.floor((distance % (1000 * 60)) / 1000)

                collectionData.value.timers[i] = {
                    state: state,
                    days: getDoubleDigitNumber(days),
                    hours: getDoubleDigitNumber(hours),
                    minutes: getDoubleDigitNumber(minutes),
                    seconds: getDoubleDigitNumber(seconds),
                }
            }
        }, 1000)
    }
}
</script>
<template>
    <MinimalLayout :loading="loading" :overlay="loading" :valid-blockchain="validBlockchain" :chain-id="collection.chain_id">
        <EmbedContent :edit-mode="editMode" :collection="collection" :collection-data="collectionData" :current-token="currentNFT" />
    </MinimalLayout>
</template>