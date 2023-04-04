<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, provide, onMounted, watch, computed, toRaw } from 'vue'
import { connectWallet } from '@/Wallets/Wallet'
import { checkCurrentBlockchain, getBlockchains } from '@/Helpers/Blockchain'
import StatusTab from '@/Components/StatusTab.vue'
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import ButtonGray from '@/Components/Form/ButtonGray.vue'
import ButtonDefault from '@/Components/Form/ButtonDefault.vue'
import LinkDarkBlue from '@/Components/LinkDarkBlue.vue'
import Button from '@/Components/Form/Button.vue'
import Textarea from '@/Components/Form/Textarea.vue'
import Input from '@/Components/Form/Input.vue'
import Label from '@/Components/Form/Label.vue'
import Checkbox from '@/Components/Form/Checkbox.vue'
import Radio from '@/Components/Form/Radio.vue'
import RadioGroup from '@/Components/Form/RadioGroup.vue'
import Addon from '@/Components/Form/Addon.vue'
import Select from '@/Components/Form/Select.vue'
import Messages from '@/Components/Messages.vue'
import { getSmartContractFromSigner, getCollectionData } from '@/Helpers/Thirdweb'
import Hyperlink from '@/Components/Hyperlink.vue'
import Modal from '@/Components/Modal.vue'
import { formateDatetimeLocal, parseClaimConditions } from '@/Helpers/Helpers'
import InputFile from '@/Components/Form/InputFile.vue'
import axios from 'axios'
import { resportError } from '@/Helpers/Sentry'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}

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
    maxTotalSupply: 0,
    totalSupply: 0,
    totalClaimedSupply: 0,
    totalRatioSupply: 0,
})
let wallet = ref(false)
let loading = ref(true)
let buttonLoading = ref(false)
let messages = ref([])
let claimPhases = ref([])
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
const form = {
    metadata: useForm({
        name: '',
        description: ''
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
        image: props.collection.seo.image
    }),
    reveal: useForm({
        delay: false,
        password: '',
        passwordConfirm: '',
        name: '',
        description: '',
        image: ''
    })
}
let socialImageLoading = ref(false)

provide('wallet', wallet)

let mintUrl = computed(() => props.collection.mint_url+'/'+form.mint.permalink)
let mintEditorUrl = computed(() => props.collection.mint_editor_url+'/'+form.mint.permalink)
let embedEditorUrl = computed(() => props.collection.embed_editor_url+'/'+form.mint.permalink)

watch(claimPhases, (newValue) => {
    disablePhases.value = disablePhases.value == null ? 1 : 0
},
{ deep: true })

onMounted(async () => {
    // Connect wallet if local storage is set
    const walletName = localStorage.getItem('walletName')
    if (walletName) {
        wallet.value = await connectWallet(walletName, false)
    }

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

        const contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
        try {

            const data = await getCollectionData(contract, props.collection.type, true, true)

            // Settings
            form.metadata.name = data.metadata.name
            form.metadata.description = data.metadata.description
            form.metadata.defaults()
            form.royalties.feeRecipient = data.royalties.feeRecipient
            form.royalties.royalties = data.royalties.royalties
            form.royalties.defaults()

            // Fees
            collectionData.value.primarySalesRecipient = data.sales.primarySalesRecipient
            collectionData.value.platformFee = data.platformFees.platformFee
            collectionData.value.platformFeeRecipient = data.platformFees.platformFeeRecipient

            // Claim phases
            claimPhases.value = parseClaimConditions(data.claimConditions)

            // Collection
            collectionData.value.totalSupply = data.totalSupply
            collectionData.value.maxTotalSupply = data.totalSupply
            collectionData.value.totalClaimedSupply = data.totalClaimedSupply
            collectionData.value.totalRatioSupply = data.totalRatioSupply
            collectionData.value.nfts = data.nfts

            // Delayed reveal
            if (props.collection.type == 'ERC721') {
                setRevealBatches(contract)
            }
        } catch(error) {
            //
        }

        // Set tab status
        validateTabStatus()
    }
})
const updateMetadata = async () => {
    // Validate form
    let error = false
    if (form.metadata.name.length < 3) {
        error = 'Collection name must be at least 3 characters long'
    } else if (form.metadata.description.length < 3) {
        error = 'Collection description must be at least 3 characters long'
    }
    if (error) {
        messages.value.push({type: 'error', message: error})
        return
    }

    buttonLoading.value = true
    const contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
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
        resportError(error)
        messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
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

    buttonLoading.value = true
    const contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
    try {
        await contract.royalties.setDefaultRoyaltyInfo({
            seller_fee_basis_points: form.royalties.royalties * 100,
            fee_recipient: form.royalties.feeRecipient,
        })

        validateSettingsTab()
        form.royalties.defaults()

        messages.value.push({type: 'success', message: 'Royalties updated'})
    } catch(error) {
        resportError(error)
        messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
    }

    buttonLoading.value = false
}
const updateMintSettings = () => {
    if (/[^A-Za-z0-9-]+/g.test(form.mint.permalink)) {
        messages.value.push({type: 'error', message: 'Only characters and dashes are allowed.'})
        return
    }

    buttonLoading.value = true

    // var data = {
    //     permalink: this.collection.permalink,
    //     title: this.collection.seo.title,
    //     description: this.collection.seo.description,
    //     image: this.collection.seo.image
    // }

    form.mint.put(route('collections.update-mint', props.collection.id), {
        onFinish: (response) => {
            validateMintPageTab()
            form.mint.defaults()

            messages.value.push({type: 'success', message: 'Mint settings updated'})
        },
        onError: (error) => {
            //
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
        } else if (claimPhase.maxClaimableSupply < 0) {
            error = claimPhase.name +': Number of NFTs is not valid'
        } else if (claimPhase.price.length < 1) {
            error = claimPhase.name +': Mint price must be a number'
        } else if (claimPhase.price < 0) {
            error = claimPhase.name +': Mint price is not valid'
        } else if (claimPhase.maxClaimablePerWallet.length < 1) {
            error = claimPhase.name +': Mints per wallet must be a number'
        } else if (claimPhase.maxClaimablePerWallet < 0) {
            error = claimPhase.name +': Mints per wallet is not valid'
        } 
        // else if (claimPhase.whitelist == true && claimPhase.maxClaimablePerWallet == 0) {
        //     error = claimPhase.name +': Claims per wallet can\'t be unlimited when you enabled a whitelist'
        // }
        if (error) {
            break
        }
    }
    if (error) {
        messages.value.push({type: 'error', message: error})
        return
    }

    buttonLoading.value = true

    let claimPhaseList = []
    for (var i = 0; i < claimPhases.value.length; i++) {
        let claimPhase = claimPhases.value[i]
        let newClaimPhase = {
            metadata: {
                name: claimPhase.name
            },
            startTime: new Date(claimPhase.startTime),
            price: parseFloat(claimPhase.price),
            maxClaimableSupply: claimPhase.maxClaimableSupply == 0 ? 'unlimited' : parseInt(claimPhase.maxClaimableSupply),
            maxClaimablePerWallet: claimPhase.maxClaimablePerWallet == 0 && claimPhase.whitelist == 0 ? 'unlimited' : parseInt(claimPhase.maxClaimablePerWallet),
            // waitInSeconds: claimPhase.waitInSeconds == 0 ? ethers.constants.MaxUint256 : 5, // Contract v2, Contract v3
            snapshot: claimPhase.whitelist == 0 ? [] : toRaw(claimPhase.snapshot),
        }
        claimPhaseList.push(newClaimPhase)
    }

    const contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
    try {
        if (props.collection.type == 'ERC721') {
            await contract.claimConditions.set(claimPhaseList)
        } else if (props.collection.type == 'ERC1155') {
            await contract.claimConditions.set(0, claimPhaseList)
        }
        validateClaimPhasesTab()
        disablePhases.value = 1
        
        messages.value.push({type: 'success', message: 'Claim phases updated'})
    } catch(error) {
        resportError(error)
        messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
    }
    
    buttonLoading.value = false
}
const updateCollection = async (e) => {
    if (form.reveal.delay) {
        // Validate form
        let error = false
        if (form.reveal.password == '') {
            error = 'Placeholder password is not valid'
        } else if (form.reveal.password != form.reveal.passwordConfirm) {
            error = 'Reveal password does not match'
        } else if (form.reveal.name == '') {
            error = 'Placeholder name is not valid'
        } else if (form.reveal.description == '') {
            error = 'Placeholder description is not valid'
        } else if (form.reveal.image == '') {
            error = 'Placeholder image is not valid'
        }
        if (error) {
            messages.value.push({type: 'error', message: error})
            return
        }
    }

    buttonLoading.value = true

    const contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
    try {
        if (form.reveal.delay) {
            await contract.revealer.createDelayedRevealBatch({
                    name: form.reveal.name,
                    description: form.reveal.description,
                    image: form.reveal.image
                },
                collectionData.value.metadata,
                form.reveal.password,
            )
            setRevealBatches(contract)
        } else if (props.collection.type == 'ERC721') {
            await contract.createBatch(collectionData.value.metadata)

            collectionData.value.totalSupply = await contract.totalSupply()
            collectionData.value.totalClaimedSupply = await contract.totalClaimedSupply()
        } else if (props.collection.type == 'ERC1155') {
            await contract.createBatch(collectionData.value.metadata)

            collectionData.value.totalSupply = await contract.call('maxTotalSupply', 0)
            collectionData.value.totalClaimedSupply = await contract.totalSupply(0)
        }
        collectionData.value.totalRatioSupply = Math.round((collectionData.value.totalClaimedSupply/collectionData.value.totalSupply)*100)
        collectionData.value.nfts = await contract.getAll({count: 8})
        collectionData.value.previews = []
        document.getElementById('image_collection').value= null
        
        if (collectionData.value.nfts.length > 0) {
            await axios.post('/collections/'+props.collection.id+'/thumb', {url: collectionData.value.nfts[0].metadata.image}).then((response) => {
                validateCollectionTab()
            })
        }
        messages.value.push({type: 'success', message: 'NFTs added to the collection!'})
    } catch(error) {
        resportError(error)
        messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
    }

    buttonLoading.value = false    
}
const updateRevealBatch = async () => {
    buttonLoading.value = true

    const contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
    try {
        await contract.revealer.reveal(collectionData.value.batchId, collectionData.value.password);

        messages.value.push({type: 'success', message: 'NFTs revealed'})
        setRevealBatches(contract)
    } catch(error) {
        resportError(error)
        messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
    }

    buttonLoading.value = false
}
const updateMaxTotalSupply = async() => {
    buttonLoading.value = true

    const contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)
    try {
        await contract.call('setMaxTotalSupply', 0, collectionData.value.maxTotalSupply)
        collectionData.value.totalSupply = collectionData.value.maxTotalSupply

        messages.value.push({type: 'success', message: 'Maximum total supply updated'})
    } catch(error) {
        resportError(error)
        messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
    }

    buttonLoading.value = false
}
const setRevealBatches = async (contract) => {
    collectionData.value.batches = {}

    const revealBatches = await contract.revealer.getBatchesToReveal()
    if (revealBatches.length) {
        for (let i = 0; i < revealBatches.length; i++) {
            let batch = revealBatches[i]
            collectionData.value.batches[batch.batchId] = batch.placeholderMetadata.name
        }
        collectionData.value.batchId = revealBatches[0].batchId
    }
}
const setPlaceholderImage = (e) => {
    let files = e.target.files
    let file = files[0]

    if(validFileType(file)) {
        form.reveal.image = file
    }
}
const addClaimPhase = () => {
    if (claimPhases.value.length >= 3) {
        messages.value.push({type: 'error', message: 'You can only have 3 mint phases'})
        return
    }
    claimPhases.value.push({
        startTime: formateDatetimeLocal(new Date(Date.now())),
        price: 0,
        maxClaimableSupply: 0,
        maxClaimablePerWallet: 0,
        whitelist: 0,
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
        var data = response.data
        claimPhases.value[index].snapshot = data
    })
}
const uploadCollection = async (event) => {
    var files = event.target.files
    var metadata = await prepareFiles(files)

    if (metadata.status == 'error') {
        messages.value.push({type: 'error', message: metadata.message})
        return;
    }

    collectionData.value.metadata = metadata.data
    collectionData.value.previews = collectionData.value.metadata.slice(0, 8)

    messages.value.push({type: 'success', message: 'NFTs received'})
}
const prepareFiles = async (files) => {
    var images = {}
    var json = {}

    for (var i = 0; i < files.length; i++) {
        var upload = files[i]
        // const extension = upload.name.slice((upload.name.lastIndexOf(".") - 1 >>> 0) + 2).toLowerCase()
        const filename = upload.name.replace(/\.[^/.]+$/, "")
        if (upload.type == 'application/json') {
            json[filename] = upload
        } else if(validFileType(upload)) {
            upload.id = filename
            upload.src = URL.createObjectURL(upload)
            images[filename] = upload
        }
    }

    var imagesLength = Object.keys(images).length
    var jsonLength = Object.keys(json).length
    if (jsonLength != imagesLength && jsonLength !== 1) {
        return {
            status: 'error',
            message: 'Images and JSON data combination is not correct',
            data: []
        }
    }
    if (props.collection.type == 'ERC1155' && (imagesLength > 1 || jsonLength > 1)) {
        return {
            status: 'error',
            message: 'Upload 1 image and 1 JSON file',
            data: []
        }
    }

    const metadata = await createMetadata(images, json)
    metadata.sort((a,b) => a.name - b.name);

    return {
        status: 'success',
        message: '',
        data: metadata
    }
}
const createMetadata = async (images, json) => {
    var imagesLength = Object.keys(images).length
    var jsonLength = Object.keys(json).length
    var firstImageKey = Object.keys(images)[0]
    var firstJsonKey = Object.keys(json)[0]
    var firstJsonFile = json[firstJsonKey]

    // Parse single JSON file
    if (jsonLength == 1) {
        var jsonList = {};
        var jsonData = await getJsonData(firstJsonFile)
        var index = parseInt(firstImageKey)
        Object.entries(jsonData).forEach((nft) => {
            jsonList[index] = nft[1]
            index++
        })
    // Parse multiple JSON files
    } else {
        var jsonList = {};
        for (var i = parseInt(firstJsonKey); i < (jsonLength + parseInt(firstJsonKey)); i++) {
            jsonList[i] = await getJsonData(json[i])
        }
    }

    // Create metadata array
    var metadata = []
    for (var i = parseInt(firstImageKey); i < (imagesLength + parseInt(firstImageKey)); i++) {
        var image = images[i]
        var json = jsonList[image.id]
        metadata.push({
            name: json.name,
            description: json.description != null && json.description != false ? json.description : '',
            image: image,
            attributes: json.attributes
        })
    }

    return metadata
}
const validFileType = (file) => {
    switch(file.type) {
        case 'image/jpeg':
        case 'image/jpg':
        case 'image/png':
        case 'image/gif':
            return true;
        default:
            return false;
    }
}
const getJsonData = async (file) => {
    return new Promise((res,rej) => {
        let reader = new FileReader()
        reader.onload = function(){
            res(JSON.parse(reader.result))
        }
        reader.readAsText(file)
    })
}
const resetWhitelist = (index) => {
    claimPhases.value[index].snapshot = []
}
const validateTabStatus = () => {
    validateSettingsTab()
    validateClaimPhasesTab()
    validateCollectionTab()
    validateMintPageTab()
}
const validateSettingsTab = () => {
    tabStatus.value.settings = 1
    if (form.metadata.name.trim() == '' || form.metadata.description.trim() == '' || form.royalties.royalties === '' || form.royalties.feeRecipient.trim() === '') {
        tabStatus.value.settings = 0
    }
}
const validateClaimPhasesTab = () => {
    tabStatus.value.phases = claimPhases.value.length > 0 ? 1 : 0
}
const validateCollectionTab = () => {
    tabStatus.value.collection = collectionData.value.nfts.length > 0 ? 1 : 0
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
        messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
    });
}
const deleteSocialImage = () => {
    form.mint.image = ''
}
</script>
<template>
    <AuthenticatedLayout :loading="loading" :overlay="buttonLoading" :valid-blockchain="validBlockchain" :chain-id="collection.chain_id">
        <Head title="Edit collection" />

        <div v-if="!wallet.account"></div>
        <div v-else>
            <form>
                <div class="text-center mb-10">
                    <h1>Manage NFT collection</h1>
                    <p>You can adjust the settings of your collection here.</p>
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
                            <Box class="flex-1 mb-4" title="General Settings" tutorial="https://www.youtube.com/embed/uSVmOgaCOlQ">
                                <BoxContent>
                                    <div class="w-full flex flex-wrap">
                                        <div class="basis-full">
                                            <Label for="name" value="Collection name" class="relative" info="This is the name of your NFT collection." />
                                            <Input type="text" id="name" class="w-full" v-model="form.metadata.name" autofocus />
                                        </div>
                                        <div class="basis-full">
                                            <Label for="description" value="Collection description" info="This should be a short description of your collection. This is displayed on marketplaces where people can trade your NFT." />
                                            <Textarea id="description" class="w-full mb-4" v-model="form.metadata.description"></Textarea>
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
                    <Box v-if="collection.type == 'ERC721' || (collection.type == 'ERC1155' && collectionData.nfts.length == 0)" class="mb-4" title="Add your collection files" tutorial="https://www.youtube.com/embed/fgzBxLpVY4E">
                        <BoxContent>
                            <p>Upload your NFT collection. If you have not yet generated your NFT collection, use our free <Hyperlink element="a" class="text-sm" href="https://generator.mintpad.co" target="_blank">NFT generator</Hyperlink> to generate your collection</p>
                            <p class="mb-4"><Hyperlink element="a" href="/examples/demo-collection.zip">Download a demo collection.</Hyperlink></p>

                            <label class="block text-mintpad-300 mb-4">
                                <span class="sr-only">Choose Files</span>
                                <InputFile @change="uploadCollection" id="image_collection" accept="application/json image/jpeg, image/png, image/jpg, image/gif" directory webkitdirectory mozdirectory multiple/>
                            </label>
                            <p>Your upload must contain images and JSON files.</p>
                        </BoxContent>
                    </Box>

                    <Box v-if="collectionData.previews.length > 0" class="mb-4" title="Preview of your collection">
                        <BoxContent>
                            <div class="grid grid-cols-4">
                                <div v-for="preview in collectionData.previews">
                                    <div class="p-1 text-sm rounded-md">
                                        <img class="w-full max-w-max transition-all duration-500 rounded-md" :src="preview.image.src" />
                                    </div>
                                </div>
                            </div>
                            <div class="w-full mt-5">
                                <p class="font-regular text-sm mb-4">Uploading the images and JSON files can take a while. Do not close this page, and wait until you get a popup from your wallet.</p>
                                <p v-if="collection.type == 'ERC721'" class="mb-4">
                                    <Checkbox id="settings-phases" class="align-middle" type="checkbox" value="1" v-model="form.reveal.delay" />
                                    <Label for="settings-phases" class="ml-2 mt-1" info="Whether the collectors will immediately see the final NFT when they complete the minting or at a later time">Delayed reveal</Label>
                                </p>
                                <div v-if="form.reveal.delay">
                                    <p class="mb-4">Collectors will mint your placeholder image so you can reveal it at a later time.</p>
                                    <div class="flex px-6 py-2 mb-4 rounded-md bg-white dark:bg-mintpad-800 border dark:border-primary-600 border-primary-200" role="alert">
                                        <i class="fa-solid fa-circle-check text-xl text-primary-600 align-middle"></i>
                                        <div class="ml-3 text-sm text-mintpad-700 dark:text-white">You will need this password to reveal your NFTs. Please store it somewhere safe.</div>
                                    </div>

                                    <div class="w-full flex flex-wrap">
                                        <div class="basis-full sm:basis-1/2 sm:pr-2">
                                            <Label for="reveal-password" value="Password" class="relative" />
                                            <Input id="reveal-password" class="mb-4" type="password" v-model="form.reveal.password" />
                                        </div>
                                        <div class="basis-full sm:basis-1/2 sm:pl-2">
                                            <Label for="reveal-confirm-password" value="Confirm password" />
                                            <Input id="reveal-confirm-password" class="mb-4" type="password" v-model="form.reveal.passwordConfirm" />
                                        </div>
                                        <div class="basis-full">
                                            <Label for="reveal-name" value="Placeholder collection name" class="relative" info="This is the placeholder name of your NFT collection." />
                                            <Input id="reveal-name" class="mb-4" type="text" v-model="form.reveal.name" />
                                        </div>
                                        <div class="basis-full">
                                            <Label for="reveal-description" value="Placeholder collection description" info="This should be a short placeholder description of your collection. This is displayed in the collectors wallet until it will be revealed." />
                                            <Textarea id="reveal-description" class="w-full mb-2" v-model="form.reveal.description"></Textarea>
                                        </div>
                                        <div class="basis-full mb-4">
                                            <Label for="description" value="Placeholder image" />
                                            <label class="block text-mintpad-300">
                                                <span class="sr-only">Choose Files</span>
                                                <InputFile @change="setPlaceholderImage" accept="image/jpeg, image/png, image/jpg, image/gif" />
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <span class="inline-block" content="This action will trigger a transaction" v-tippy>
                                    <Button href="#" @click.prevent="updateCollection" :loading="buttonLoading">Add to collection</Button>
                                </span>
                            </div>
                        </BoxContent>
                    </Box>

                    <Box class="mb-4" title="Your collection">
                        <BoxContent>
                            <div class="text-sm">
                                <p v-if="collectionData.nfts.length == 0">Your collection is still empty.</p>
                                <p v-else-if="collection.type == 'ERC1155' && collectionData.totalSupply == 0">{{ collectionData.totalClaimedSupply }} minted out of an unlimited supply.</p>
                                <p v-else>Total minted {{ collectionData.totalRatioSupply }}% ({{ collectionData.totalClaimedSupply}}/{{ collectionData.totalSupply }})</p>
                                <div class="grid grid-cols-4 mt-2">
                                    <div class="p-1 text-center text-sm" v-for="nft in collectionData.nfts">
                                        <img class="w-full max-w-max transition-all duration-500 rounded-md" :src="nft.metadata.image" />
                                    </div>
                                </div> 
                            </div> 
                        </BoxContent>
                    </Box>

                    <Box v-if="collection.type == 'ERC721'" title="Reveal your NFTs">
                        <BoxContent>
                            <div v-if="Object.keys(collectionData.batches).length" class="mb-4">
                                <div class="basis-full">
                                    <Label value="Batch name" class="relative" info="Which batch of NFTs you want to reveal." />
                                    <Select class="!w-full mb-4" v-model="collectionData.batchId" :options="collectionData.batches"></Select>
                                </div>
                                <div>
                                    <Label value="Password" class="relative" info="Password that was entered while creating this batch." />
                                    <Input class="w-full" type="password" v-model="collectionData.password" />
                                </div>
                            </div>
                            <p v-else class="mb-4">You don't have any NFTs to reveal</p>

                            <span class="inline-block" content="This action will trigger a transaction" v-tippy>
                                <Button href="#" @click.prevent="updateRevealBatch" :loading="buttonLoading" :disabled="!Object.keys(collectionData.batches).length">Reveal NFTs</Button>
                            </span>
                        </BoxContent>
                    </Box>
                </div>

                <div v-show="currentTab == 3">
                    <Box class="mb-4" title="Mint phases" tutorial="https://www.youtube.com/embed/syNDd3Iepy4">
                        <BoxContent>
                            <p>On this page you can set mint phases. You can set whitelist phases and the public mint. <b>You must have set at least one mint phase with a maximum of 3.</b></p>
                            <p>When you only set one mint phase, this will be the date and time that people can mint your collection.</p>
                        </BoxContent>
                    </Box>

                    <Box v-if="collection.type == 'ERC1155' && collectionData.nfts.length == 0" class="mb-4">
                        <BoxContent>
                            <p class="">You need to upload an NFT first. You can do this in the <Hyperlink href="#" element="a" @click.prevent.native="changeStatusTab(2)">upload collection</Hyperlink> section.</p>
                        </BoxContent>
                    </Box>

                    <Box v-if="collection.type == 'ERC1155' && collectionData.nfts.length > 0" title="Set maximum total supply">
                        <BoxContent>
                            <div>
                                <Label value="Maximum total supply" class="relative" info="The max number of NFTs that can be minted. (0 = unlimited)." />
                                <Input class="w-full" type="text" v-model="collectionData.maxTotalSupply" />
                            </div>

                            <span class="inline-block" content="This action will trigger a transaction" v-tippy>
                                <Button href="#" @click.prevent="updateMaxTotalSupply" :loading="buttonLoading">Update</Button>
                            </span>
                        </BoxContent>
                    </Box>

                    <div v-if="(collection.type == 'ERC1155' && collectionData.nfts.length > 0) || collection.type == 'ERC721'">
                        <Box v-for="(phase, index) in claimPhases" class="mb-4" :title="'Phase '+(index+1)">
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
                                        <Input type="number" v-model="phase.maxClaimableSupply" />
                                    </div>
                                    <div class="relative">
                                        <Label value="Mint price" info="The mint price people pay for one NFT from your collection." />
                                        <Addon position="right" :content="currentBlockchain.nativeCurrency.symbol">
                                            <Input class="addon-right" step="0.001" type="number" v-model="phase.price" />
                                        </Addon>
                                    </div>
                                    <div>
                                        <Label value="Claims per wallet" info="The number of NFTs that can be minted per wallet in this mint phase. (0 = unlimited)." />
                                        <Input type="number" v-model="phase.maxClaimablePerWallet" />
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
                                    <div v-if="phase.whitelist == 1" class="col-span-2">
                                        <Label value="Whitelist CSV file" info="Here you can upload a .CSV file with all whitelisted wallets." />
                                        <p class="text-sm"><ButtonGray href="#" @click.prevent="toggleWhitelistModal(index, true)">Upload CSV</ButtonGray><span class="ml-3" v-html="phase.snapshot.length"></span> addresses</p>
                                    </div>

                                    <Modal title="Whitelist addresses" :show="phase.modal" @close.prevent="toggleWhitelistModal(index, false)">
                                        <div class="overflow-y-auto" :class="{'max-h-80 bg-primary-100 dark:bg-mintpad-800 rounded-md border border-primary-200 dark:border-mintpad-900': phase.snapshot != 0}">
                                            <div v-if="phase.snapshot != 0" class="p-4">
                                                <p v-for="walletAddress in phase.snapshot">{{ walletAddress.address }}</p>
                                            </div>
                                            <div v-else>
                                                <p>Here you can upload a .CSV file with all whitelisted wallets. Not sure what your .CSV should contain?</p>
                                                <p class="mb-4"><Hyperlink href="/examples/snapshot.csv">Download a demo whitelist.</Hyperlink></p>
                                                <label class="block mb-4 text-mintpad-300">
                                                    <span class="sr-only">Choose File</span>
                                                    <InputFile @change="uploadWhitelist($event, index)" />
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

                        <Box v-if="claimPhases.length == 0" class="mb-4">
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
                    <Box class="mb-4" title="Mint settings" tutorial="https://www.youtube.com/embed/MqVxSbt33xQ">
                        <BoxContent>
                            <p>Here you can customize your mint page. Add SEO to your page and customize the design.</p>
                        </BoxContent>
                    </Box>

                    <Box class="mb-4" title="Permalink">
                        <BoxContent>
                            <Label for="permalink" value="Permalink" />
                            <Addon position="left" :content="collection.mint_url+'/'">
                                <Input id="permalink" class="basis-1/3 addon-left" position="left" type="text" v-model="form.mint.permalink" />
                            </Addon>
                            <LinkDarkBlue element="a" :href="mintEditorUrl" target="_blank" class="mr-2">Page editor</LinkDarkBlue>
                            <LinkDarkBlue element="a" :href="embedEditorUrl" target="_blank" class="mr-2">Embed editor</LinkDarkBlue>
                            <LinkDarkBlue element="a" :href="mintUrl" target="_blank">View collection page</LinkDarkBlue>
                        </BoxContent>
                    </Box>

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