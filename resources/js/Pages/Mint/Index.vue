<script setup>
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import Hyperlink from '@/Components/Hyperlink.vue'
import { getDummyCollection, parseClaimConditions, setStyling, shortenWalletAddress, getDoubleDigitNumber, WeiToValue, fileIsImage, fileIsVideo, calculateTransactionFee } from '@/Helpers/Helpers'
import MinimalLayout from '@/Layouts/MinimalLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, provide, onMounted, inject } from 'vue'
import axios from 'axios'
import { checkCurrentBlockchain, getBlockchains } from '@/Helpers/Blockchain'
import DarkMode from '@/Components/DarkMode.vue'
import { getCollectionData, getSmartContract, getSmartContractFromSigner } from '@/Helpers/Thirdweb'
import { getMetaMaskError, switchChainTo } from '@/Wallets/MetaMask'
import { connectWallet, getDefaultWalletData, reconnectWallet } from '@/Wallets/Wallet'
import Modal from '@/Components/Modal.vue'
import EditorBar from '@/Pages/Mint/Partials/EditorBar.vue'
import ButtonEditor from '@/Pages/Mint/Partials/ButtonEditor.vue'
import LogoEditor from '@/Pages/Mint/Partials/LogoEditor.vue'
import Messages from '@/Components/Messages.vue'
import { reportError } from '@/Helpers/Sentry'
import { ethers } from 'ethers'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}

const props = defineProps({
    collection: Object,
    seo: Object,
    mode: String,
    token: String
})
let loading = ref(true)
let wallet = ref(getDefaultWalletData())
let validBlockchain = ref(true)
let buttonLoading = ref(false)
let messages = ref([])
const testlink = ref('');


let collectionData = ref({
    contractType: 'ERC721',
    id: props.collection.id,
    buttons: {},
    logo: null,
    background: null,
    thumb: false,
    theme: {
        primary: {r: 0, g: 119, b: 255, a: 1}
    },
    claimPhases: [],
    balance: {tier1: '...', tier2: '...'},
    nftsToBurn: 0,
    transactionFee: 0,
    totalSupply: '...',
    totalRatioSupply: 100,
    royalties: '...',
    nfts: []
})
let editMode = ref(props.mode == 'edit' ? true : false)
let loadComplete = ref(false)
let activeMintPhase = ref(null)
let mintAmount = ref(1)
let maxMintAmount = ref(1)
let blockchains = ref(getBlockchains())
let timers = ref({})
let showModal = ref(false)
let currentNFT = ref(props.token)
const emitter = inject('emitter')

provide('wallet', wallet)

onMounted(async () => {
    // Connect wallet
    wallet.value = await reconnectWallet()

    // Init app
    validBlockchain.value = checkCurrentBlockchain(blockchains, props.collection.chain_id, wallet)

    if (props.collection.type == 'ERC1155') {
        props.collection.name = ''
    }
 

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

        if (editMode.value) {
            const dummyData = getDummyCollection()
            collectionData.value.claimPhases = dummyData.claimPhases
            timers.value = dummyData.timers
            collectionData.value.totalSupply = dummyData.collection.totalSupply
            collectionData.value.totalClaimedSupply = dummyData.collection.totalClaimedSupply
            collectionData.value.totalRatioSupply = dummyData.collection.totalRatioSupply
            collectionData.value.nftsToBurn = 2
            collectionData.value.balance = {
                tier1: 5,
                tier2: 1
            }
        } else {
            // Set contract
            let contract
            if (wallet.value.account && validBlockchain.value === true) {
                contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
            } else {
                contract = await getSmartContract(props.collection.chain_id, props.collection.address, props.collection.type)
            }
                  //console log wallet adddress
          const account = wallet.value.account;
            console.log("player",account);
            //console log contract address
            console.log("house",props.collection.address);
      
            //console log the collection names and types
            console.log("housename",props.collection.name);
            console.log("housetype",props.collection.type);
     
            
// some changes here
          
            try {
                let getNFTAmount = props.collection.type == 'ERC1155' ? 1000 : 1
                const data = await getCollectionData(contract, props.collection.type, true, getNFTAmount, currentNFT.value)            
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
                
                // // Claim phases
                collectionData.value.claimPhases = parseClaimConditions(data.claimConditions)
                setClaimPhaseCounters()
                setActiveClaimPhase()

                // Collection
                collectionData.value.nfts = data.nfts
                if (collectionData.value.nfts.length) {
                    collectionData.value.thumb = collectionData.value.nfts[currentNFT.value].metadata.image

                    if (props.collection.type == 'ERC1155') {
                        props.collection.name = collectionData.value.nfts[currentNFT.value].metadata.name
                    }
                } else {
                    if (props.collection.type == 'ERC1155') {
                        props.collection.name = collectionData.value.name
                    }
                }

                loadComplete.value = true
                
            } catch (error) {
                console.log('mint 1', error)
                reportError(error)
                messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
            }

        }
    }).catch((error) => {
        console.log('mint 2', error)
        //
    })
})

//player details

  

const switchBlockchain = async () => {
    const status = await switchChainTo(props.collection.chain_id)
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
        collectionData.value.totalClaimedSupply = await contract.totalSupply(currentNFT.value)
        collectionData.value.balance = {
            tier1: await contract.balanceOf(wallet.value.account, '0'),
            tier2: await contract.balanceOf(wallet.value.account, '1')
        }
    }
    collectionData.value.totalRatioSupply = Math.round((collectionData.value.totalClaimedSupply/collectionData.value.totalSupply)*100)
    if (isNaN(collectionData.value.totalRatioSupply)) {
        collectionData.value.totalRatioSupply = 0
    }
}
const setActiveClaimPhase = () => {
    for (var i = 0; i < collectionData.value.claimPhases.length; i++) {
        var claimPhase = collectionData.value.claimPhases[i]
        var from = new Date(claimPhase.startTime).getTime()
        var to = new Date(claimPhase.endTime).getTime()
        var now = new Date().getTime()
        if (now <= to && now >= from) {
            collectionData.value.claimPhases[i].active = true
            maxMintAmount.value = claimPhase.maxClaimablePerWallet
            activeMintPhase.value = i
        } else if (now >= from && to == 0) {
            collectionData.value.claimPhases[i].active = true
            maxMintAmount.value = claimPhase.maxClaimablePerWallet
            activeMintPhase.value = i
        } else {
            collectionData.value.claimPhases[i].active = false
        }
    }
}
const setClaimPhaseCounters = () => {
    for (var i = 0; i < collectionData.value.claimPhases.length; i++) {
        timers.value[i] = {}
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
        timers.value[i] = false
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

            if (endDate === 0 && distance < 0) {
                clearInterval(x)
                timers.value[i] = Infinity
            } else if (distance < 0) {
                clearInterval(x)
                timers.value[i] = false
            
            // Coming or runing phases
            } else {
                var days = Math.floor(distance / (1000 * 60 * 60 * 24))
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
                var seconds = Math.floor((distance % (1000 * 60)) / 1000)

                timers.value[i] = {
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


const mintNFT = async () => {
    try {
        if (editMode.value) {
            showModal.value = true;
            return;
        }

        if (collectionData.value.claimPhases.length === 0) {
            messages.value.push({ type: 'error', message: 'You cannot mint this NFT yet because no mint phases have been set yet' });
            return;
        }

        buttonLoading.value = true;

        // Set contract
        const contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type);

        let txHash;

        if (props.collection.type === 'ERC721') {
            if (collectionData.value.contractType === 'DropERC721') {
                const tx = await contract.claim(mintAmount.value);
                txHash = tx.hash;
            } else {
                const preparedClaim = await contract.claim.prepare(mintAmount.value);
                const overrideValue = preparedClaim.overrides.value === undefined ? 0 : WeiToValue(preparedClaim.overrides.value);
                preparedClaim.overrides.value = calculateTransactionFee(collectionData.value.transactionFee, overrideValue);

                if (!wallet.value.balance.value.gte(preparedClaim.overrides.value)) {
                    throw new Error("Insufficient funds for transaction");
                }

                const sentTx = await preparedClaim.send();
                txHash = sentTx.hash;
            }
        } else if (props.collection.type.startsWith('ERC1155')) {
            if (collectionData.value.contractType === 'DropERC1155') {
                const tx = await contract.claim(0, mintAmount.value);
                txHash = tx.hash;
            } else {
                const preparedClaim = await contract.claim.prepare(currentNFT.value, mintAmount.value);
                const overrideValue = preparedClaim.overrides.value === undefined ? 0 : WeiToValue(preparedClaim.overrides.value);
                preparedClaim.overrides.value = calculateTransactionFee(collectionData.value.transactionFee, overrideValue);

                if (!wallet.value.balance.value.gte(preparedClaim.overrides.value)) {
                    throw new Error("Insufficient funds for transaction");
                }

                const sentTx = await preparedClaim.send();
                txHash = sentTx.hash;
            }
        }

        console.log('Transaction hash:', txHash);
        testlink.value = txHash;
        console.log('testlink', testlink.value);
        const serializableWallet = {
            account: wallet.value.account,
            balance: wallet.value.balance,
     
        };

        // conditional polling trigger based on chainId
        const chainId = 167000;
        console.log(props.collection.chain_id);
        if (chainId === props.collection.chain_id) {
         // trigger the polling
         // check for cors origin later
            const response = await fetch('https://on.mintpad.co/startPolling', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    txHash,
                    wallet: serializableWallet,
                    collection: props.collection,
           
                    
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to start polling');
            }

            console.log('Polling started successfully');
        } else {
          // some changes to made here, like show modal ?
            console.log('Chain ID does not match. No polling triggered.');
        }

        showModal.value = true;

    } catch (error) {
        console.error('Error:', error);
        messages.value.push({ type: 'error', message: 'Something went wrong, please try again.' });
        buttonLoading.value = false;
    }
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

        <EditorBar :edit-mode="editMode" :collection-data="collectionData" />
        <div class="w-full h-96 bg-black/[.35] dark:bg-mintpad-800/[.35] bg-top bg-cover bg-blend-multiply" :class="{'sm:mt-14': editMode}" :style="[collectionData.background ? {backgroundImage: 'url(' + collectionData.background + ')'} : {}]">
            <div class="relative max-w-7xl mx-auto px-6 pb-4 h-full flex gap-4 items-end">
                <LogoEditor :edit-mode="editMode" :collection-data="collectionData" />
                <DarkMode class="absolute top-4 right-6"></DarkMode>

                <div v-if="collectionData.thumb" class="h-24 sm:h-36 md:h-48 bg-white rounded-md p-1 text-center">
                    <img v-if="collectionData.thumb && fileIsImage(collectionData.thumb)" class="inline-block rounded-m h-full" :src="collectionData.thumb" />
                    <video v-if="collectionData.thumb && fileIsVideo(collectionData.thumb)" class="inline-block rounded-m h-full" autoplay loop muted>
                        <source :src="collectionData.thumb" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <div v-else class="h-24 sm:h-36 md:h-48 w-24 sm:w-36 md:w-48 bg-white rounded-md p-1 text-center">
                    <div class="bg-white dark:bg-mintpad-500 w-full h-full">
                        <div v-if="!loadComplete" class="bg-primary-300 mint-bg-primary-sm rounded-md w-full h-full animate-pulse"></div>
                    </div>
                </div>
                <h2 v-if="collection.name" class="grow text-lg sm:text-2xl md:text-5xl text-white">{{ collection.name }}</h2>
                <div v-else-if="!loadComplete" class="bg-primary-300 mint-bg-primary-sm rounded-md w-1/3 h-10 mb-4 animate-pulse"></div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 mt-12">
            <div v-if="collectionData.claimPhases.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-x-4">
                <Box v-for="(phase, index) in collectionData.claimPhases" class="min-h-[12rem]" :title="phase.name">
                    <template v-slot:action>
                        <span v-if="phase.active" class="inline-block absolute top-3.5 right-4 sm:w-auto mx-0 sm:mx-3 px-4 py-1 text-xs border border-green-600 bg-green-100 text-green-600 dark:text-green-600 dark:border-0 dark:bg-[#0F391D] rounded-full">Active</span>
                    </template>
                    <BoxContent>
                        <div class="grid grid-cols-1 sm:grid-cols-2">
                            <p v-if="phase.maxClaimableSupply === 0">NFTs: <span class="text-primary-600 mint-text-primary font-medium">unlimited</span></p>
                            <p v-else>NFTs: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.maxClaimableSupply"></span></p>
                            <p>Price: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.price"></span> <span class="text-primary-600 mint-text-primary font-medium" v-html="blockchains[collection.chain_id].nativeCurrency.symbol"></span></p>
                            <p v-if="phase.maxClaimablePerWallet === 0">Max claims: <span class="text-primary-600 mint-text-primary font-medium">unlimited</span></p>
                            <p v-else>Max claims: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.maxClaimablePerWallet"></span></p>
                            <p v-if="phase.whitelist">Whitelist: <span class="text-primary-600 mint-text-primary font-medium" v-html="phase.snapshot.length"></span></p>
                            <p v-else>Whitelist: <span class="text-primary-600 mint-text-primary font-medium">No</span></p>
                        </div>
                        <div v-if="typeof timers[index] === 'object' && timers[index].state != undefined" class="mt-3 text-sm text-mintpad-700">
                            <div class="relative w-full text-right sm:text-center font-regular">
                                <p class="absolute left-0 top-2 font-medium text-mintpad-500"><span v-html="timers[index].state"></span> in</p>
                                <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="timers[index].days"></span>
                                <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="timers[index].hours"></span>
                                <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="timers[index].minutes"></span>
                                <span class="bg-primary-300 text-mintpad-200 mint-bg-primary rounded px-1.5 py-2 mr-1 inline-block w-8" v-html="timers[index].seconds"></span>
                            </div>
                        </div>
                        <p v-else-if="typeof timers[index] !== 'object' && timers[index] !== Infinity" class="mt-6 text-sm text-mintpad-700 font-medium">Phase ended</p>
                    </BoxContent>
                </Box>
            </div>
            <Box v-else-if="collectionData.claimPhases.length == 0 && loadComplete" class="w-full" title="Mint phases">
            <BoxContent>
                <p>Minting is disabled because no mint phases are active at the moment.</p>
            </BoxContent>
        </Box>
        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-x-4">
            <Box v-for="(phase, index) in [1,2,3]" class="min-h-[12rem]" :title="'Phase '+(index+1)">
                <BoxContent>
                    <div class="bg-primary-300 mint-bg-primary-sm rounded-md w-1/2 h-5 mb-4 animate-pulse"></div>
                    <div class="bg-primary-300 mint-bg-primary-sm rounded-md w-full h-5 mb-4 animate-pulse"></div>
                    <div class="bg-primary-300 mint-bg-primary-sm rounded-md w-2/3 h-5 animate-pulse"></div>
                </BoxContent>
            </Box>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4">
            <Box class="sm:col-span-2" title="Mint an NFT">
                <BoxContent>
                    <form>
                        <p class="font-regular text-center mb-4">Start minting by clicking the button below</p>
                        <div v-if="editMode" class="flex gap-2">
                            <Input type="number" value="1" class="!mb-0 !w-28" />   
                            <Button @click.prevent="mintNFT" class="w-full mint-bg-primary !py-2" :loading="buttonLoading">Start minting</Button>
                        </div>
                        <div v-else class="flex gap-2">
                            <Input type="number" v-model="mintAmount" min="1" :max="maxMintAmount == 0 ? 99999 : maxMintAmount" class="!mb-0 !w-28" />
                            <Button v-if="!wallet.account" @click.prevent="connectWallet('metamask')" class="w-full mint-bg-primary !py-2">Connect MetaMask</Button>
                            <Button v-else-if="validBlockchain !== true" @click.prevent="switchBlockchain" class="w-full mint-bg-primary !py-2">Switch blockchain</Button>
                            <Button v-else="" @click.prevent="mintNFT" :loading="buttonLoading" :disabled="collectionData.claimPhases.length == 0" class="w-full mint-bg-primary !py-2">Start minting</Button>
                        </div>
                        
                        <div v-if="collectionData.claimPhases.length > 0" class="grid sm:grid-cols-2 mt-4 text-sm font-medium">
                            <div>
                                <p>Total minted</p>
                            </div>
                            <div class="text-right">
                                <!-- Only show percentage if the wallet is connected -->
                                <p v-if="wallet.account">
                                    <span v-if="collection.type.startsWith('ERC1155') && collectionData.totalSupply == 0">{{ collectionData.totalClaimedSupply }}</span>
                                    <span v-else>{{ collectionData.totalRatioSupply }}% ({{ collectionData.totalClaimedSupply}}/{{ collectionData.totalSupply }})</span>
                                </p>
                                <p v-else>{{ collectionData.totalClaimedSupply }} / {{ collectionData.totalSupply }}</p>
                            </div>
                        </div>
                        <!-- Show progress bar only if wallet is connected -->
                        <div v-if="wallet.account && collectionData.claimPhases.length > 0" class="w-full mt-2 rounded-full bg-primary-300 mint-bg-primary-sm">
                            <div class="rounded-full bg-primary-600 mint-bg-primary p-1" :style="{width: collectionData.totalRatioSupply + '%'}"></div>
                        </div>
                    </form>
                </BoxContent>
            </Box>
            <Box title="Collection details">
                <BoxContent>
                    <div class="grid grid-cols-2 gap-1">
                        <p>Contract address</p>
                        <p class="font-medium !text-primary-600 mint-text-primary">
                            <a :href="blockchains[collection.chain_id].explorers[0].url+'/address/'+collection.address" target="_blank" class="underline">{{ shortenWalletAddress(collection.address) }}</a>
                        </p>
                        <p>Collection Size</p>
                        <p class="font-medium !text-primary-600 mint-text-primary" v-html="collectionData.totalSupply == 0 ? 'Unlimited' : collectionData.totalSupply"></p>
                        <p>Creator Royalties</p>
                        <p class="font-medium !text-primary-600 mint-text-primary" v-html="collectionData.royalties"></p>
                        <p>Type</p>
                        <p class="font-medium !text-primary-600 mint-text-primary">{{ collection.type }}</p>
                        <p>Blockchain</p>
                        <p class="font-medium !text-primary-600 mint-text-primary" v-html="blockchains[collection.chain_id].name"></p>
                        <p>Transaction fee</p>
                      <!-- <p class="font-medium !text-primary-600 mint-text-primary">{{ collectionData.contractType == 'DropERC721' || collectionData.contractType == 'DropERC1155' ? '-' : '~0.00$' }}</p> -->

                        <p v-if="collection.type == 'ERC1155Burn'">Your tier 1 NFTs</p>
                        <p v-if="collection.type == 'ERC1155Burn'" class="font-medium !text-primary-600 mint-text-primary" v-html="collectionData.balance.tier1"></p>
                        <p v-if="collection.type == 'ERC1155Burn'">Your tier 2 NFTs</p>
                        <p v-if="collection.type == 'ERC1155Burn'" class="font-medium !text-primary-600 mint-text-primary" v-html="collectionData.balance.tier2"></p>
                    </div>
                </BoxContent>
            </Box>
            <Box v-if="editMode || collectionData.buttons.length" class="sm:col-span-3">
                <BoxContent>
                    <ButtonEditor :edit-mode="editMode" :collection-data="collectionData" />
                </BoxContent>
            </Box>
            <Box v-if="collection.description != ''" class="sm:col-span-3" title="Description">
                <BoxContent>
                    <p class="font-regular">{{ collection.description }}</p>
                </BoxContent>
            </Box>
            <div v-if="props.collection.type == 'ERC1155' && collectionData.nfts.length > 1" class="text-center my-4 sm:col-span-3">
                <h3>All NFTs in this collection</h3>
            </div>
            <Box v-if="props.collection.type == 'ERC1155' && collectionData.nfts.length > 1" v-for="(nft, index) in collectionData.nfts" :title="nft.metadata.name">
                <BoxContent>
                    <Link :href="route('mint.index', [collection.permalink, index])">
                        <div v-if="nft.metadata.image" class="w-full rounded-md">
                            <img v-if="nft.metadata.image && fileIsImage(nft.metadata.image)" class="inline-block rounded-m h-full rounded-md" :src="nft.metadata.image" />
                            <video v-if="nft.metadata.image && fileIsVideo(nft.metadata.image)" class="inline-block rounded-m h-full rounded-md" autoplay loop muted>
                                <source :src="nft.metadata.image" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div v-else class="h-24 sm:h-36 md:h-48 w-24 sm:w-36 md:w-48 bg-white rounded-md p-1 text-center">
                            <i class="inline-block text-black mt-10 sm:mt-16 md:mt-20 text-lg fa-solid fa-spinner animate-spin"></i>
                        </div>
                    </Link>
                </BoxContent>
            </Box>
        </div>

        <div class="inline-block w-full mt-4 mb-16 text-center">
            <Hyperlink element="a" href="https://mintpad.co/terms-of-service/" target="_blank" class="text-sm !text-mintpad-700 dark:!text-mintpad-200 border border-mintpad-200 dark:border-mintpad-900 bg-white dark:bg-mintpad-800 rounded-md p-3 px-6">Terms of Service</Hyperlink>
        </div>
    </div>

    <div v-if="editMode" class="fixed left-0 bottom-0 p-2 w-full bg-primary-600 text-white">
        <div class="max-w-3xl lg:max-w-5xl mx-auto px-6 lg:px-0">
            <p class="!text-white text-center font-medium text-sm !mb-0">We use demo data for showcase purposes</p>
        </div>
    </div>

    <Modal :show="showModal" title="Mint successful!" @close="showModal = false">
        <p>Congratulations. You have successfully minted.</p>
    
    </Modal>

        <Messages :messages="messages" />
    </MinimalLayout>  
</template>
