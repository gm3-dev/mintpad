<script setup>
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import Button from '@/Components/Form/Button.vue'
import Hyperlink from '@/Components/Hyperlink.vue'
import { setStyling, shortenWalletAddress, WeiToValue, fileIsImage, fileIsVideo } from '@/Helpers/Helpers'
import MinimalLayout from '@/Layouts/MinimalLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, provide, onMounted, inject } from 'vue'
import axios from 'axios'
import { checkCurrentBlockchain, getBlockchains } from '@/Helpers/Blockchain'
import DarkMode from '@/Components/DarkMode.vue'
import { getCollectionData, getSmartContract, getSmartContractFromSigner } from '@/Helpers/Thirdweb'
import { getMetaMaskError, switchChainTo } from '@/Wallets/MetaMask'
import { connectWallet, getDefaultWalletData, reconnectWallet } from '@/Wallets/Wallet'
import Modal from '@/Components/Modal.vue'
import ButtonEditor from '@/Pages/Mint/Partials/ButtonEditor.vue'
import Messages from '@/Components/Messages.vue'
import { resportError } from '@/Helpers/Sentry'
import { ethers } from 'ethers'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}

const props = defineProps({
    collection: Object,
    seo: Object,
    mode: String
})
let loading = ref(true)
let wallet = ref(getDefaultWalletData())
let validBlockchain = ref(true)
let buttonLoading = ref(false)
let messages = ref([])

let collectionData = ref({
    contractType: 'ERC721',
    id: props.collection.id,
    buttons: {},
    logo: null,
    background: null,
    thumb: { src: null },
    theme: {
        primary: {r: 0, g: 119, b: 255, a: 1}
    },
    balance: {tier1: '...', tier2: '...'},
    nftsToBurn: 0,
    transactionFee: 0,
    totalSupply: '...',
    royalties: '...',
    nfts: []
})
let loadComplete = ref(false)
let blockchains = ref(getBlockchains())
let showModal = ref(false)
const emitter = inject('emitter')

provide('wallet', wallet)

onMounted(async () => {
    // Connect wallet
    wallet.value = await reconnectWallet()

    buttonLoading.value = true

    // Init app
    validBlockchain.value = checkCurrentBlockchain(blockchains, props.collection.chain_id, wallet)

    axios.get('/collection/'+props.collection.id+'/fetch').then(async (response) => {
        collectionData.value.buttons = setButtons(response.data.buttons ?? [])
        collectionData.value.logo = response.data.logo
        collectionData.value.background = response.data.background
        // collectionData.value.thumb.src = response.data.thumb
        
        // Set theme for mint
        if (response.data.theme.mint) {
            collectionData.value.theme = response.data.theme.mint
        }

        setStyling(collectionData.value)

        // Done loading
        loading.value = false

        // Set contract
        let contract
        if (wallet.account && validBlockchain === true) {
            contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
        } else {
            contract = await getSmartContract(props.collection.chain_id, props.collection.address, props.collection.type)
        }
        try {
            const data = await getCollectionData(contract, props.collection.type, true, 2)            
            const contractType = await contract.call('contractType')

            // Settings
            collectionData.value.contractType = ethers.utils.parseBytes32String(contractType)
            collectionData.value.name = data.metadata.name
            collectionData.value.feeRecipient = data.royalties.feeRecipient
            collectionData.value.royalties = data.royalties.royalties+'%'
            if (collectionData.value.contractType == 'DropERC721' || collectionData.value.contractType == 'DropERC1155') {
                collectionData.value.transactionFee = 0
            } else {
                let transactionFee = await contract.call('getTransactionFee')
                collectionData.value.transactionFee = WeiToValue(transactionFee.toString())
            }

            // Fees
            collectionData.value.primarySalesRecipient = data.sales.primarySalesRecipient

            // Collection supply
            collectionData.value.nftsToBurn = data.nftsToBurn
            setSupplyData(contract)
            setInterval(() => {
                setSupplyData(contract)
            }, 10000)

            // Collection
            collectionData.value.nfts = data.nfts

            loadComplete.value = true
            
        } catch (error) {
            console.log('mint 1', error)
            resportError(error)
            messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
        } 
        buttonLoading.value = false

    }).catch((error) => {
        buttonLoading.value = false
        console.log('mint 2', error)
        //
    })
})

const switchBlockchain = async () => {
    const status = await switchChainTo(props.chainId)
    if (status !== true) {
        emitter.emit('new-message', {type: 'error', message: status})
    }
}
const setSupplyData = async (contract) => {
    if (props.collection.type == 'ERC721') {
        collectionData.value.totalSupply = await contract.totalSupply()
        collectionData.value.totalClaimedSupply = await contract.totalClaimedSupply()
        collectionData.value.balance = {
            tier1: await contract.balanceOf(wallet.value.account),
            tier2: 0
        }
    } else if (props.collection.type.startsWith('ERC1155')) {
        collectionData.value.totalSupply = await contract.call('maxTotalSupply', [0], {})
        collectionData.value.totalClaimedSupply = await contract.totalSupply('0')
        collectionData.value.balance = {
            tier1: await contract.balanceOf(wallet.value.account, 0),
            tier2: await contract.balanceOf(wallet.value.account, 1)
        }
    }
    collectionData.value.totalRatioSupply = Math.round((collectionData.value.totalClaimedSupply/collectionData.value.totalSupply)*100)
    if (isNaN(collectionData.value.totalRatioSupply)) {
        collectionData.value.totalRatioSupply = 0
    }
}
const setButtons = (buttons) => {
    var output = []
    for (var i = 0; i < buttons.length; i++) {
        var button = buttons[i]
        try {
            new URL(button.href);
        } catch (error) {
            button.href = 'https://'+button.href
        }
        output.push(button)
    }
    return output
}
const burnNFTs = async (e) => {
    buttonLoading.value = true
    try {
        const contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
        const firstClaimPhase = await contract.call('getClaimConditionById', [0, 0], {})
        // let valueOverride = (collectionData.value.transactionFee * 1000000000000000000).toString()
        let valueOverride = ethers.utils.parseUnits(collectionData.value.transactionFee).toString()
        await contract.call('evolve', [wallet.value.account, firstClaimPhase.currency], {
            value: valueOverride
        })

        showModal.value = true

        setSupplyData(contract)
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
    <MinimalLayout :loading="loading" :overlay="loading" :valid-blockchain="validBlockchain" :chain-id="collection.chain_id">
        <Head>
            <title>{{ seo.title }}</title>
            <meta name="description" :content="seo.description">
            <!-- Twitter -->
            <meta v-if="seo.image" name="twitter:image:src" :content="seo.image">
            <meta name="twitter:site" content="@mintpadco">
            <meta name="twitter:card" content="summary">
            <meta name="twitter:title" :content="seo.title">
            <meta name="twitter:description" :content="seo.description">
            <!-- Open Graph -->
            <meta v-if="seo.image" property="og:image" :content="seo.image">
            <meta v-if="seo.image" property="og:image:alt" :content="seo.title">
            <meta property="og:site_name" content="Mintpad.co">
            <meta property="og:type" content="object">
            <meta property="og:title" :content="seo.title">
            <meta property="og:url" :content="route().current()">
            <meta property="og:description" :content="seo.description">
        </Head>

        <div class="w-full h-96 bg-black/[.35] dark:bg-mintpad-800/[.35] bg-top bg-cover bg-blend-multiply" :style="[collectionData.background ? {backgroundImage: 'url(' + collectionData.background + ')'} : {}]">
            <div class="relative max-w-7xl mx-auto px-6 pb-4 h-full flex gap-4 items-end">
                <DarkMode class="absolute top-4 right-6"></DarkMode>

                <div v-if="collectionData.nfts.length > 0" class="h-24 sm:h-36 md:h-48 bg-white rounded-md p-1 text-center">
                    <img v-if="collectionData.nfts[1].metadata.image && fileIsImage(collectionData.nfts[1].metadata.image)" class="inline-block rounded-m h-full" :src="collectionData.nfts[1].metadata.image" />
                    <video v-if="collectionData.nfts[1].metadata.image && fileIsVideo(collectionData.nfts[1].metadata.image)" class="inline-block rounded-m h-full" autoplay loop>
                        <source :src="collectionData.nfts[1].metadata.image" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <div v-else class="h-24 sm:h-36 md:h-48 w-24 sm:w-36 md:w-48 bg-white rounded-md p-1 text-center">
                    <i class="inline-block text-black mt-10 sm:mt-16 md:mt-20 text-lg fa-solid fa-spinner animate-spin"></i>
                </div>
                <h2 class="grow text-lg sm:text-2xl md:text-5xl text-white">{{ collection.name }}</h2>
            </div>
        </div>
        <div v-if="collection.type == 'ERC1155Burn'" class="max-w-7xl mx-auto px-6 mt-12">                
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4">
                <Box class="sm:col-span-2" title="Burn your NFTs">
                    <BoxContent>
                        <form>
                            <div class="text-center">
                                <p class="my-4">You can evolve your NFTs by burning <b>{{ collectionData.nftsToBurn }}</b> NFTs.</p>
                                
                                <Button v-if="!wallet.account" @click.prevent="connectWallet('metamask')" class="w-full mint-bg-primary !py-2">Connect MetaMask</Button>
                                <Button v-else-if="validBlockchain !== true" @click.prevent="switchBlockchain" class="w-full mint-bg-primary !py-2">Switch blockchain</Button>
                                <Button v-else @click.prevent="burnNFTs" :loading="buttonLoading" class="w-full mint-bg-primary !py-2">Burn <b>{{ collectionData.nftsToBurn }}</b> NFTs</Button>

                                <span v-for="(nft, index) in collectionData.nfts" class="inline-block mt-6 rounded-md">
                                    <img v-if="nft.metadata.image && fileIsImage(nft.metadata.image)" class="inline-block rounded-md w-20 h-full" :src="nft.metadata.image" />
                                    <video v-if="nft.metadata.image && fileIsVideo(nft.metadata.image)" class="inline-block rounded-md w-20 h-full" autoplay loop>
                                        <source :src="nft.metadata.image" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <span v-if="index == 0" class="inline-block mx-4"><i class="fa-solid fa-arrow-right"></i></span>
                                </span>
                            </div>
                        </form>
                    </BoxContent>
                </Box>
                <Box title="Collection details">
                    <BoxContent>
                        <div class="grid grid-cols-2 gap-1">
                            <p>Contract address</p><p class="font-medium !text-primary-600 mint-text-primary"><a :href="blockchains[collection.chain_id].explorers[0].url+'/address/'+collection.address" target="_blank" class="underline">{{ shortenWalletAddress(collection.address) }}</a></p>
                            <p>Collection Size</p><p class="font-medium !text-primary-600 mint-text-primary" v-html="collectionData.totalSupply == 0 ? 'Unlimited' : collectionData.totalSupply"></p>
                            <p>Creator Royalties</p><p class="font-medium !text-primary-600 mint-text-primary" v-html="collectionData.royalties"></p>
                            <p>Type</p><p class="font-medium !text-primary-600 mint-text-primary">{{ collection.type }}</p>
                            <p>Blockchain</p><p class="font-medium !text-primary-600 mint-text-primary" v-html="blockchains[collection.chain_id].name"></p>
                            <p>Transaction fee</p><p class="font-medium !text-primary-600 mint-text-primary">{{ collectionData.contractType == 'DropERC721' || collectionData.contractType == 'DropERC1155'? '-' : '~1$' }}</p>
                            <p>Your tier 1 NFTs</p><p class="font-medium !text-primary-600 mint-text-primary" v-html="collectionData.balance.tier1"></p>
                            <p>Your tier 2 NFTs</p><p class="font-medium !text-primary-600 mint-text-primary" v-html="collectionData.balance.tier2"></p>
                            <p>Burn ratio</p><p class="font-medium !text-primary-600 mint-text-primary" v-html="collectionData.nftsToBurn+':1'"></p>
                        </div>
                    </BoxContent>
                </Box>
                <Box v-if="collectionData.buttons.length" class="sm:col-span-3">
                    <BoxContent>
                        <ButtonEditor edit-mode="false" :collection-data="collectionData" />
                    </BoxContent>
                </Box>
                <Box v-if="collection.description != ''" class="sm:col-span-3" title="Description">
                    <BoxContent>
                        <p class="font-regular">{{ collection.description }}</p>
                    </BoxContent>
                </Box>
            </div>

            <div class="inline-block w-full mt-4 mb-16 text-center">
                <Hyperlink element="a" href="https://mintpad.co/terms-of-service/" target="_blank" class="text-sm !text-mintpad-700 dark:!text-mintpad-200 border border-mintpad-200 dark:border-mintpad-900 bg-white dark:bg-mintpad-800 rounded-md p-3 px-6">Terms of Service</Hyperlink>
            </div>
        </div>
        <div v-else class="max-w-7xl mx-auto px-6 mt-12">
            <p>This collection can't be burned.</p>
        </div>

        <Modal :show="showModal" title="Mint successful!" @close="showModal = false">
            <p>You have an NFT in your wallet! You can now trade this NFT on OpenSea and other marketplaces.</p>
            <p class="!text-primary-600 mint-text-primary">Good luck with trading!</p>
        </Modal>

        <Messages :messages="messages" />
    </MinimalLayout>  
</template>