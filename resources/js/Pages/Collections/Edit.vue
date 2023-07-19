<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, provide, onMounted, watch, computed, toRaw, inject } from 'vue'
import { getDefaultWalletData, reconnectWallet } from '@/Wallets/Wallet'
import { checkCurrentBlockchain, getBlockchains } from '@/Helpers/Blockchain'
import StatusTab from '@/Components/StatusTab.vue'
import InfoMessage from '@/Components/Messages/InfoMessage.vue'
import ErrorMessage from '@/Components/Messages/ErrorMessage.vue'
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import ButtonGray from '@/Components/Form/ButtonGray.vue'
import ButtonDefault from '@/Components/Form/ButtonDefault.vue'
import LinkDarkBlue from '@/Components/LinkDarkBlue.vue'
import Button from '@/Components/Form/Button.vue'
import Textarea from '@/Components/Form/Textarea.vue'
import Input from '@/Components/Form/Input.vue'
import Label from '@/Components/Form/Label.vue'
import Radio from '@/Components/Form/Radio.vue'
import RadioGroup from '@/Components/Form/RadioGroup.vue'
import Addon from '@/Components/Form/Addon.vue'
import Messages from '@/Components/Messages.vue'
import { getSmartContractFromSigner, getCollectionData, getClaimPhases, getTotalsData } from '@/Helpers/Thirdweb'
import Hyperlink from '@/Components/Hyperlink.vue'
import Modal from '@/Components/Modal.vue'
import { formateDatetimeLocal, parseClaimConditions, handleError, shortenWalletAddress, copyToClipboard, ipfsToUrl } from '@/Helpers/Helpers'
import InputFile from '@/Components/Form/InputFile.vue'
import axios from 'axios'
import { reportError } from '@/Helpers/Sentry'
import ERC1155Burn from './Partials/Collection/ERC1155Burn.vue'
import ERC721 from './Partials/Collection/ERC721.vue'
import ERC1155 from './Partials/Collection/ERC1155.vue'
import Select from '@/Components/Form/Select.vue'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}

const emitter = inject('emitter')

const props = defineProps({
    collection: Object,
    errors: Object
})
let collectionData = ref({
    nfts: [],
    previews: [],
    metadata: [],
    batches: {},
    batchId: 0,
    password: '',
    totalSupply: 0,
    totalClaimedSupply: 0,
    totalRatioSupply: 0,
})
let wallet = ref(getDefaultWalletData())
let loading = ref(true)
let buttonLoading = ref(false)
let messages = ref([])
let claimPhases = ref([])
let claimPhasesLoading = ref(true)
let disablePhases = ref(null)
let blockchains = ref(getBlockchains())
let currentBlockchain = ref(blockchains.value[props.collection.chain_id])
let validBlockchain = ref(false)
let currentTab = ref(0)
let tabStatus = ref({
    settings: 2,
    phases: 2,
    collection: 2,
    mint: 2
})
let currentNFT = ref(0)
let NFTList = ref([])
let contract = false
const form = {
    metadata: useForm({
        name: '',
        salesRecipient: ''
    }),
    royalties: useForm({
        feeRecipient: '',
        royalties: ''
    }),
    mint: useForm({
        permalink: props.collection.permalink,
        seo: {
            title: props.collection.seo.title,
            description: props.collection.seo.description
        },
        image: props.collection.seo.image,
        description: props.collection.description
    }),
    totalsupply: useForm({
        max: 0
    })
}
let socialImageLoading = ref(false)

emitter.on('set-tab-status', (data) => {
    tabStatus.value[data.tab] = data.status
})

provide('wallet', wallet)
provide('transaction', {show: true, message: ''})

let burnUrl = computed(() => props.collection.mint_url+'/'+form.mint.permalink+'/burn')
let mintUrl = computed(() => props.collection.mint_url+'/'+form.mint.permalink)
let mintEditorUrl = computed(() => props.collection.mint_editor_url+'/'+form.mint.permalink)
let embedEditorUrl = computed(() => props.collection.embed_editor_url+'/'+form.mint.permalink)

watch(claimPhases, (newValue) => {
    disablePhases.value = disablePhases.value == null ? 1 : 0
},
{ deep: true })

onMounted(async () => {
    // Connect wallet
    wallet.value = await reconnectWallet()

    // Init app
    validBlockchain.value = checkCurrentBlockchain(blockchains, props.collection.chain_id, wallet)

    // Done loading
    loading.value = false

    // Check if wallet is connected to the correct blockchain
    if (validBlockchain.value !== true) {
        currentTab.value = -1
        messages.value.push({type: 'error', message: 'There is a problem with your wallet'})
    } else {
        currentTab.value = 1

        contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
        await setContractData()
        
        // Set tab status
        validateTabStatus()
    }
})
const updateManager = async (data) => {
    if (data.phases) {
        await setContractData()
    }
}
const setContractData = async () => {
    try {
        const data = await getCollectionData(contract, props.collection.type, true, 1000, currentNFT.value)

        // Settings
        form.metadata.name = data.metadata.name
        form.metadata.salesRecipient = data.sales.primarySalesRecipient
        form.metadata.defaults()
        form.royalties.feeRecipient = data.royalties.feeRecipient
        form.royalties.royalties = data.royalties.royalties
        form.royalties.defaults()

        // Fees
        collectionData.value.primarySalesRecipient = data.sales.primarySalesRecipient

        // NFT list
        for (var i = 0; i < data.nfts.length; i++) {
            NFTList.value[i] = data.nfts[i].metadata.name
        }

        // Claim phases
        claimPhases.value = parseClaimConditions(data.claimConditions)
        claimPhasesLoading.value = false

        // Total supply
        form.totalsupply.max = data.totalSupply
        form.totalsupply.defaults()
    } catch(error) {
        messages.value.push({type: 'error', message: handleError(error)})
    }
}
const changeCurrentNFT = async () => {
    claimPhases.value = []
    claimPhasesLoading.value = true

    // Claim phases
    claimPhases.value = parseClaimConditions(await getClaimPhases(contract, props.collection.type, false, currentNFT.value))
    
    // Total supply
    const totals = await getTotalsData(contract, props.collection.type, currentNFT.value)
    form.totalsupply.max = totals.totalSupply
    form.totalsupply.defaults()

    claimPhasesLoading.value = false
    disablePhases.value = 1
}
const updateMetadata = async () => {
    // Validate form
    let error = false
    if (form.metadata.name.length < 3) {
        error = 'Collection name must be at least 3 characters long'
    }
    if (form.metadata.salesRecipient.length < 10) {
        error = 'Recipient address is not valid'
    }
    if (error) {
        messages.value.push({type: 'error', message: error})
        return
    }

    buttonLoading.value = 'Updating settings'
    try {
        await contract.metadata.set({
            name: form.metadata.name,
            description: form.metadata.description
        })

        form.metadata.put(route('collections.update-metadata', props.collection.id), {
            onFinish: () => {
                validateSettingsTab()
            }
        })
        form.metadata.defaults()

        messages.value.push({type: 'success', message: 'General settings updated'})
    } catch(error) {
        messages.value.push({type: 'error', message: handleError(error)})
    }

    buttonLoading.value = false
}
const updateRoyalties = async () => {
    // Validate form
    let error = false
    if (form.royalties.royalties.length < 1) {
        error = 'Creator royalties must be a number'
    } else if (form.royalties.royalties < 0 || form.royalties.royalties > 100) {
        error = 'Creator royalties must be a number between 0 and 100'
    } else if (form.royalties.feeRecipient.length < 10) {
        error = 'Recipient address is not valid'
    }
    if (error) {
        messages.value.push({type: 'error', message: error})
        return
    }

    buttonLoading.value = 'Updating royalties'
    try {
        await contract.royalties.setDefaultRoyaltyInfo({
            seller_fee_basis_points: form.royalties.royalties * 100,
            fee_recipient: form.royalties.feeRecipient,
        })

        validateSettingsTab()
        form.royalties.defaults()

        messages.value.push({type: 'success', message: 'Royalties updated'})
    } catch(error) {
        messages.value.push({type: 'error', message: handleError(error)})
    }

    buttonLoading.value = false
}
const updateMintSettings = () => {
    if (/[^A-Za-z0-9-]+/g.test(form.mint.permalink)) {
        messages.value.push({type: 'error', message: 'Only characters and dashes are allowed.'})
        return
    }

    buttonLoading.value = 'Updating mint settings'

    form.mint.put(route('collections.update-mint', props.collection.id), {
        preserveScroll: true,
        onSuccess: () => {
            validateMintPageTab()
            form.mint.defaults()

            messages.value.push({type: 'success', message: 'Mint settings updated'})
        },
        onFinish: (response) => {
            //
        },
        onError: (error) => {
            for (var key in error) {
                if (error[key]) {
                    messages.value.push({type: 'error', message: error[key]})
                }
            }
        }
    })

    buttonLoading.value = false
}
const updateClaimPhases = async () => {
    // Validate form
    let error = false
    for (var i = 0; i < claimPhases.value.length; i++) {
        let claimPhase = claimPhases.value[i]
        
        if (claimPhase.name.length < 1) {
            error = 'Phase '+ claimPhase.id +': Mint phase name must be at least 1 character long'
        } else if (claimPhase.maxClaimableSupply.length < 1) {
            error = claimPhase.name +': Number of NFTs must be a number'
        } else if (parseInt(claimPhase.maxClaimableSupply) < 0) {
            error = claimPhase.name +': Number of NFTs is not valid'
        } else if (claimPhase.price.length < 1) {
            error = claimPhase.name +': Mint price must be a number'
        } else if (claimPhase.price < 0) {
            error = claimPhase.name +': Mint price is not valid'
        } else if (claimPhase.maxClaimablePerWallet.length < 1) {
            error = claimPhase.name +': Mints per wallet must be a number'
        } else if (parseInt(claimPhase.maxClaimablePerWallet) < 0) {
            error = claimPhase.name +': Mints per wallet is not valid'
        } 

        if (error) {
            break
        }
    }
    if (error) {
        messages.value.push({type: 'error', message: error})
        return
    }

    buttonLoading.value = 'Updating mint phases'

    let claimPhaseList = []
    for (var i = 0; i < claimPhases.value.length; i++) {
        let claimPhase = claimPhases.value[i]
        // Set maxClaimablePerWallet
        let maxClaimablePerWallet = claimPhase.maxClaimablePerWallet == '0' && claimPhase.whitelist == '0' ? 'unlimited' : parseInt(claimPhase.maxClaimablePerWallet)
        if (claimPhase.whitelist == '1') {
            for (var s = 0; s < claimPhase.snapshot.length; s++) {
                claimPhase.snapshot[s].maxClaimable = maxClaimablePerWallet
            }
            maxClaimablePerWallet = '0'
        } else {
            claimPhase.snapshot = []
        }
        let newClaimPhase = {
            metadata: {
                name: claimPhase.name
            },
            startTime: new Date(claimPhase.startTime),
            price: parseFloat(claimPhase.price),
            maxClaimableSupply: claimPhase.maxClaimableSupply == '0' ? 'unlimited' : parseInt(claimPhase.maxClaimableSupply),
            maxClaimablePerWallet: maxClaimablePerWallet,
            // waitInSeconds: claimPhase.waitInSeconds == 0 ? ethers.constants.MaxUint256 : 5, // Contract v2, Contract v3
            snapshot: claimPhase.whitelist == '0' ? [] : toRaw(claimPhase.snapshot),
        }
        claimPhaseList.push(newClaimPhase)
    }

    try {
        if (props.collection.type == 'ERC721') {
            await contract.claimConditions.set(claimPhaseList)
        } else if (props.collection.type.startsWith('ERC1155')) {
            await contract.claimConditions.set(currentNFT.value, claimPhaseList)
        }
        validateClaimPhasesTab()
        disablePhases.value = 1
        
        messages.value.push({type: 'success', message: 'Claim phases updated'})
    } catch(error) {
        messages.value.push({type: 'error', message: handleError(error)})
    }
    
    buttonLoading.value = false
}
const updateMaxTotalSupply = async() => {
    buttonLoading.value = 'Updating maximum total supply'

    try {
        await contract.call('setMaxTotalSupply', [currentNFT.value, form.totalsupply.max], {})
        form.totalsupply.defaults()

        messages.value.push({type: 'success', message: 'Maximum total supply updated'})
    } catch(error) {
        messages.value.push({type: 'error', message: handleError(error)})
    }

    buttonLoading.value = false
}
const addClaimPhase = () => {
    if (claimPhases.value.length >= 3) {
        messages.value.push({type: 'error', message: 'You can only have 3 mint phases'})
        return
    }
    claimPhases.value.push({
        startTime: formateDatetimeLocal(new Date(Date.now())),
        price: 0,
        maxClaimableSupply: '0',
        maxClaimablePerWallet: '0',
        whitelist: '0',
        // waitInSeconds: 1, Contract v2, Contract v3
        snapshot: [],
        modal: false,
        name: 'Phase ' + (claimPhases.value.length + 1)
    })
}
const deleteClaimPhase = (index) => {
    if (index > -1) {
        claimPhases.value.splice(index, 1)
    }
}
const uploadWhitelist = async (e, index) => {
    var files = e.target.files
    var formData = new FormData()
    formData.append('file', files[0])
    await axios.post('/collections/'+props.collection.id+'/whitelist', formData).then((response) => {
        claimPhases.value[index].snapshot = response.data.addresses
        claimPhases.value[index].snapshotDuplicates = response.data.duplicates
        claimPhases.value[index].snapshotInvalid = response.data.invalid
    })
}

const resetWhitelist = (index) => {
    claimPhases.value[index].snapshot = []
    claimPhases.value[index].snapshotDuplicates = 0
    claimPhases.value[index].snapshotInvalid = []
}
const validateTabStatus = () => {
    validateSettingsTab()
    // validateCollectionTab()
    validateClaimPhasesTab()
    validateMintPageTab()
}
const validateSettingsTab = () => {
    tabStatus.value.settings = 1
    if (form.metadata.name.trim() == '' || form.metadata.salesRecipient.trim() === '' || form.royalties.royalties === '' || form.royalties.feeRecipient.trim() === '') {
        tabStatus.value.settings = 0
    }
}
const validateClaimPhasesTab = () => {
    tabStatus.value.phases = claimPhases.value.length > 0 ? 1 : 0
}
const validateMintPageTab = () => {
    tabStatus.value.mint = 1
    if (form.mint.permalink.trim() === '' || form.mint.seo.title.trim() === '' || form.mint.seo.description.trim() === '') {
        tabStatus.value.mint = 0
    }
}
const changeStatusTab = (tab) => {
    currentTab.value = tab
}
const previousStatusTab = () => {
    currentTab.value = currentTab.value == 1 ? currentTab.value : currentTab.value - 1
}
const nextStatusTab = () => {
    currentTab.value = currentTab.value == 4 ? currentTab.value : currentTab.value + 1
}
const toggleWhitelistModal = (index, state) => {
    claimPhases.value[index].modal = state
}
const addSocialImage = (e) => {
    socialImageLoading.value = true
    var files = e.target.files
    var formData = new FormData()
    formData.append('resource', files[0])
    formData.append('name', 'social-sharing')

    axios.post(route('resources.upload', props.collection.id), formData)
    .then((response) => {
        form.mint.image = response.data.url
        socialImageLoading.value = false
    }).catch((error) => {
        messages.value.push({type: 'error', message: handleError(error)})
    });
}
const deleteSocialImage = () => {
    form.mint.image = ''
}
</script>
<template>
    <AuthenticatedLayout :loading="loading" :transaction="buttonLoading" :valid-blockchain="validBlockchain" :chain-id="collection.chain_id">
        <Head title="Edit collection" />

        <div v-if="!wallet.account"></div>
        <div v-else>
            <form>
                <div class="text-center mb-10">
                    <h1>{{ collection.name }}</h1>
                    <p>You can adjust the settings of your collection here.</p>
                    <p>
                        <ButtonGray content="Copy contract address" @click.prevent="copyToClipboard" :text="collection.address" class="!w-40 !text-sm !px-3 !py-1" v-tippy><i class="fas fa-copy mr-2 text-mintpad-700 dark:text-white"></i>{{ shortenWalletAddress(collection.address) }}</ButtonGray>
                        <span class="inline-block align-middle font-medium px-3 py-1 text-xs bg-mintpad-200 border border-transparent dark:bg-mintpad-700 text-mintpad-700 dark:text-mintpad-200 rounded-md text-center ml-4"><img v-if="currentBlockchain.icon" class="inline-block mr-2 h-5" :src="ipfsToUrl(currentBlockchain.icon.url)" /> {{ currentBlockchain.name }}</span>
                        <span class="inline-block align-middle font-medium px-3 py-1.5 text-xs bg-mintpad-200 border border-transparent dark:bg-mintpad-700 text-mintpad-700 dark:text-mintpad-200 rounded-md text-center ml-4">{{ collection.type }}</span>
                    </p>
                </div>

                <div v-if="validBlockchain === true">
                    <div class="w-full grid grid-cols-2 gap-4 sm:block mb-8 text-left sm:text-center">
                        <StatusTab @click.prevent.native="changeStatusTab(1)" :label="'Settings'" :complete="tabStatus.settings"></StatusTab>
                        <StatusTab @click.prevent.native="changeStatusTab(2)" :label="'Upload collection'" :complete="tabStatus.collection"></StatusTab>
                        <StatusTab @click.prevent.native="changeStatusTab(3)" :label="'Mint phases'" :complete="tabStatus.phases"></StatusTab>
                        <StatusTab @click.prevent.native="changeStatusTab(4)" :label="'Mint settings'" :complete="tabStatus.mint"></StatusTab>
                    </div>
                </div>
                <div v-if="currentTab > 0" class="w-full mb-6 text-center">
                    <ButtonGray href="#" @click.prevent="previousStatusTab" :class="{'!text-mintpad-400': currentTab == 1}">Previous step</ButtonGray>
                    <h2 v-if="currentTab == 1" class="hidden sm:inline-block text-2xl w-1/4">Settings</h2>
                    <h2 v-if="currentTab == 2" class="hidden sm:inline-block text-2xl w-1/4">Upload collection</h2>
                    <h2 v-if="currentTab == 3" class="hidden sm:inline-block text-2xl w-1/4">Mint phases</h2>
                    <h2 v-if="currentTab == 4" class="hidden sm:inline-block text-2xl w-1/4">Mint settings</h2>
                    <ButtonGray href="#" @click.prevent="nextStatusTab" :class="{'!text-mintpad-400': currentTab == 4}">Next step</ButtonGray>
                </div>
                <div v-show="currentTab == 1">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <Box class="flex-1 mb-4" title="General Settings">
                                <BoxContent>
                                    <div class="w-full flex flex-wrap">
                                        <div class="basis-full">
                                            <Label for="name" value="Collection name" class="relative" info="This is the name of your NFT collection." />
                                            <Input type="text" id="name" class="w-full" v-model="form.metadata.name" autofocus />
                                        </div>
                                        <div class="basis-full">
                                            <Label for="sales_recipient" value="Sales recipient address" class="relative" info="This is the wallet address where the revenue from initial sales of your NFT collection go." />
                                            <Input id="sales_recipient" class="w-full" v-model="form.metadata.salesRecipient" />
                                        </div>
                                    </div>
                                </BoxContent>
                            </Box>
                            <div class="w-full mb-8">
                                <span class="inline-block" content="This action will trigger a transaction" v-tippy>
                                    <Button href="#" @click.prevent="updateMetadata" :disabled="!form.metadata.isDirty" :loading="buttonLoading">Update settings</Button>
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <Box class="flex-1 mb-4" title="Royalties">
                                <BoxContent>
                                    <div>
                                        <Label for="fee_recipient" value="Recipient address" class="relative" info="This is the wallet address where the proceeds of your NFT collection go. By default, this is the wallet address that puts the NFT collection on the blockchain. Double check this address." />
                                        <Input id="fee_recipient" class="w-full" v-model="form.royalties.feeRecipient" />
                                    </div>
                                    <div class="w-1/2">
                                        <Label for="royalties" value="Creator royalties (%)" class="relative" info="This is how much percent you want to receive from secondary sales on marketplaces such as OpenSea and Magic Eden." />
                                        <Addon position="right" content="%">
                                            <Input type="number" id="royalties" addon="%" class="w-full addon-right" step=".01" v-model="form.royalties.royalties" />
                                        </Addon>
                                    </div>
                                </BoxContent>
                            </Box>
                            <div class="w-full mb-8">
                                <span class="inline-block" content="This action will trigger a transaction" v-tippy>
                                    <Button href="#" @click.prevent="updateRoyalties" :disabled="!form.royalties.isDirty" :loading="buttonLoading">Update royalties</Button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-show="currentTab == 2">
                    <ERC721 v-if="collection.type == 'ERC721'" :collection="collection" />
                    <ERC1155 v-if="collection.type == 'ERC1155'" :collection="collection" :updater="updateManager"/>
                    <ERC1155Burn v-if="collection.type == 'ERC1155Burn'" :collection="collection" />
                </div>

                <div v-show="currentTab == 3">
                    <Box class="mb-4" title="Mint phases" documentation="https://docs.mintpad.co/written-tutorials/mint-phases">
                        <BoxContent>
                            <p>On this page you can set mint phases. You can set whitelist phases and the public mint. <b>You must have set at least one mint phase with a maximum of 3.</b></p>
                            <p>When you only set one mint phase, this will be the date and time that people can mint your collection.</p>
                        </BoxContent>
                    </Box>

                    <Box v-if="collection.type.startsWith('ERC1155') && tabStatus.collection !== 1" class="mb-4">
                        <BoxContent>
                            <p class="">You need to upload an NFT first. You can do this in the <Hyperlink href="#" element="a" @click.prevent.native="changeStatusTab(2)">upload collection</Hyperlink> section.</p>
                        </BoxContent>
                    </Box>

                    <Box v-if="collection.type == 'ERC1155' && NFTList.length">
                        <BoxContent>
                            <Label value="Select a NFT to edit" class="relative" />
                            <Select class="!w-full mb-4" @change="changeCurrentNFT" v-model="currentNFT" :options="NFTList"></Select>
                        </BoxContent>
                    </Box>

                    <Box v-if="!claimPhasesLoading && collection.type.startsWith('ERC1155') && tabStatus.collection == 1" title="Set maximum total supply">
                        <BoxContent>
                            <div>
                                <Label value="Maximum total supply" class="relative" info="The max number of NFTs that can be minted. (0 = unlimited)." />
                                <Input class="w-full" type="text" v-model="form.totalsupply.max" />
                            </div>

                            <span class="inline-block" content="This action will trigger a transaction" v-tippy>
                                <Button href="#" @click.prevent="updateMaxTotalSupply" :disabled="!form.totalsupply.isDirty" :loading="buttonLoading">Update</Button>
                            </span>
                        </BoxContent>
                    </Box>

                    <div v-if="(collection.type.startsWith('ERC1155') && tabStatus.collection == 1) || collection.type == 'ERC721'">
                        <Box v-if="!claimPhasesLoading" v-for="(phase, index) in claimPhases" class="mb-4" :title="'Phase '+(index+1)">
                            <template v-slot:action>
                                <a href="#" class="absolute right-8 top-3 text-xs font-medium text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="deleteClaimPhase(index)">Delete phase</a>
                            </template>
                            <BoxContent>
                                <div class="w-full grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4">
                                    <div>
                                        <Label value="Phase start time" info="This is the time and date when people can mint your NFT collection. Note: This time is shown in your local time." />
                                        <Input type="datetime-local" v-model="phase.startTime" />
                                    </div>
                                    <div>
                                        <Label value="Number of NFTs" info="The number of NFTs that will be released in this mint phase. (0 = unlimited)." />
                                        <Input type="number" min="1" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" v-model="phase.maxClaimableSupply" />
                                    </div>
                                    <div class="relative">
                                        <Label value="Mint price" info="The mint price people pay for one NFT from your collection." />
                                        <Addon position="right" :content="currentBlockchain.nativeCurrency.symbol">
                                            <Input class="addon-right" step="0.001" type="number" v-model="phase.price" />
                                        </Addon>
                                    </div>
                                    <div>
                                        <Label value="Claims per wallet" info="The number of NFTs that can be minted per wallet in this mint phase. (0 = unlimited)." />
                                        <Input type="number" min="1" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" v-model="phase.maxClaimablePerWallet" />
                                    </div>
                                    <div>
                                        <Label value="Phase name" info="Here you can give this mint phase a name. This is only visible on your own mint page." />
                                        <Input type="text" v-model="phase.name" />
                                    </div>
                                    <div>
                                        <Label value="Enable whitelist" info="Here you can choose whether to enable a whitelist or not." />
                                        <RadioGroup>
                                            <Radio :id="'whitelist-0-'+index" type="radio" v-model="phase.whitelist" value="0" class="inline-block" /><Label :for="'whitelist-0-'+index" class="inline-block mx-2" value="No" />
                                            <Radio :id="'whitelist-1-'+index" type="radio" v-model="phase.whitelist" value="1" class="inline-block" /><Label :for="'whitelist-1-'+index" class="inline-block mx-2" value="Yes" />
                                        </RadioGroup>
                                    </div>
                                    <div v-if="phase.whitelist == '1'" class="col-span-2">
                                        <Label value="Whitelist CSV file" info="Here you can upload a .CSV file with all whitelisted wallets." />
                                        <p class="text-sm"><ButtonGray href="#" @click.prevent="toggleWhitelistModal(index, true)">Upload CSV</ButtonGray><span class="ml-3" v-html="phase.snapshot.length"></span> addresses</p>
                                    </div>

                                    <Modal title="Whitelist addresses" :show="phase.modal" @close="toggleWhitelistModal(index, false)">
                                        <InfoMessage v-if="phase.snapshotDuplicates" class="text-mintpad-700 dark:text-white">We removed {{ phase.snapshotDuplicates }} duplicate address(es).</InfoMessage>
                                        <ErrorMessage v-if="phase.snapshotInvalid && phase.snapshotInvalid.length">
                                            <p class="text-mintpad-700 dark:text-white">We removed {{ phase.snapshotInvalid.length }} invalid address(es):</p>
                                            <p v-for="invalidAddress in phase.snapshotInvalid" class="!text-white !mb-0 font-mono">{{ invalidAddress }}</p>
                                        </ErrorMessage>
                                        <div class="overflow-y-auto" :class="{'max-h-80 bg-primary-100 dark:bg-mintpad-800 rounded-md border border-primary-200 dark:border-mintpad-900': phase.snapshot != 0}">
                                            <div v-if="phase.snapshot != 0" class="p-4">
                                                <p v-for="walletAddress in phase.snapshot" class="font-mono">{{ walletAddress.address }}</p>
                                            </div>
                                            <div v-else>
                                                <p>Here you can upload a .CSV file with all whitelisted wallets. Not sure what your .CSV should contain?</p>
                                                <p class="mb-4"><Hyperlink href="/examples/snapshot.csv">Download a demo whitelist.</Hyperlink></p>
                                                <label class="block mb-4 text-mintpad-300">
                                                    <span class="sr-only">Choose File</span>
                                                    <InputFile @change="uploadWhitelist($event, index)" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                                </label>
                                                <p>Upload a .CSV file. One wallet address per row.</p>
                                            </div>
                                        </div>
                                        <div v-if="phase.snapshot != 0" class="w-full mt-4">
                                            <ButtonGray href="#" @click.prevent="resetWhitelist(index)">Delete</ButtonGray>
                                            <Button href="#" @click.prevent="toggleWhitelistModal(index, false)" class="ml-2">Save</Button>
                                        </div>
                                    </Modal>
                                </div>
                            </BoxContent>
                        </Box>

                        <Box v-if="claimPhasesLoading" class="mb-4">
                            <BoxContent>
                                <p>Loading...</p>
                            </BoxContent>
                        </Box>
                        <Box v-else-if="claimPhases.length == 0" class="mb-4">
                            <BoxContent>
                                <p class="">You have no mint phases set yet.</p>
                            </BoxContent>
                        </Box>

                        <div class="w-full text-center mb-4">
                            <ButtonDefault href="#" @click.prevent="addClaimPhase"><i class="fa-solid fa-plus mr-2 text-lg align-middle"></i> Add another mint phase</ButtonDefault>
                        </div>
                        <div class="w-full">
                            <span class="inline-block" content="This action will trigger a transaction" v-tippy>
                                <Button href="#" @click.prevent="updateClaimPhases" :disabled="disablePhases" :loading="buttonLoading">Update mint phases</Button>
                            </span>
                        </div>
                        
                    </div>
                </div>
                <div v-show="currentTab == 4">
                    <Box class="mb-4" title="Mint settings">
                        <BoxContent>
                            <p>Here you can customize your mint page. Add SEO to your page and customize the design.</p>
                        </BoxContent>
                    </Box>

                    <div class="grid grid-cols-2 gap-4">
                        <Box title="Permalink">
                            <BoxContent>
                                <Label for="permalink" value="Permalink" />
                                <Addon position="left" :content="collection.mint_url+'/'">
                                    <Input id="permalink" class="basis-1/3 addon-left" position="left" type="text" v-model="form.mint.permalink" />
                                </Addon>
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 text-center">
                                    <LinkDarkBlue element="a" :href="mintEditorUrl" target="_blank" class="!px-0">Page editor</LinkDarkBlue>
                                    <LinkDarkBlue element="a" :href="embedEditorUrl" target="_blank" class="!px-0">Embed editor</LinkDarkBlue>
                                    <LinkDarkBlue element="a" :href="mintUrl" target="_blank" class="!px-0">Collection page</LinkDarkBlue>
                                    <LinkDarkBlue v-if="collection.type == 'ERC1155Burn'" element="a" :href="burnUrl" target="_blank" class="!px-0">Burn page</LinkDarkBlue>
                                </div>
                            </BoxContent>
                        </Box>

                        <Box title="Description">
                            <BoxContent>
                                <Label for="description" value="Collection description" info="This should be a short description of your collection. This is displayed on your mint page." />
                                <Textarea id="description" class="w-full" v-model="form.mint.description"></Textarea>                            
                            </BoxContent>
                        </Box>
                    </div>

                    <Box class="mb-4" title="SEO settings">
                        <BoxContent>
                            <div class="w-full grid grid-cols-2 gap-4">
                                <div>
                                    <div>
                                        <Label for="seo-title" value="Title" info="This is the title that is displayed on search engine result pages, browser tabs, and social media. You can use up to 60 characters." />
                                        <Input id="seo-title" class="w-full" type="text" v-model="form.mint.seo.title" />
                                    </div>

                                    <div>
                                        <Label for="seo-description" value="Description" info="This is the description that is displayed on search engine result pages, browser tabs, and social media. You can use up to 155 characters." />
                                        <Textarea id="seo-description" class="w-full mb-4" v-model="form.mint.seo.description"></Textarea>
                                    </div>
                                </div>
                                <div>
                                    <Label for="seo-image" value="Social share image" info="This is the thumbnail people see when you share your mintpage link. Thumbnail image size is 1280x720px." />
                                    <div v-if="form.mint.image" class="relative">
                                        <ButtonGray href="#" class="absolute top-0 right-0 m-2 !px-3 !py-2 text-lg" @click.prevent="deleteSocialImage"><i class="fas fa-trash-alt"></i></ButtonGray>
                                        <img :src="form.mint.image" class="rounded-md" />
                                    </div>
                                    <div v-else class="mb-4">
                                        <p v-if="socialImageLoading" class="mt-1"><i class="fa-solid fa-cloud-arrow-up animate-bounce mr-2 text-lg"></i> uploading...</p>
                                        <div v-else>
                                            <InputFile id="upload-logo" @change="addSocialImage" accept="image/jpeg, image/png, image/jpg" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </BoxContent>
                    </Box>

                    <div class="w-full">
                        <Button @click.prevent="updateMintSettings" :disabled="!form.mint.isDirty" :loading="buttonLoading">Update mint settings</Button>
                    </div>
                </div>
            </form>
        </div>

        <Messages :messages="messages"/>
    </AuthenticatedLayout>
</template>