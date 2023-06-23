<script setup>
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import Button from '@/Components/Form/Button.vue'
import Checkbox from '@/Components/Form/Checkbox.vue'
import Input from '@/Components/Form/Input.vue'
import InputFile from '@/Components/Form/InputFile.vue'
import Label from '@/Components/Form/Label.vue'
import Select from '@/Components/Form/Select.vue'
import Textarea from '@/Components/Form/Textarea.vue'
import Hyperlink from '@/Components/Hyperlink.vue'
import { getCollectionData, getSmartContractFromSigner } from '@/Helpers/Thirdweb'
import { useForm } from '@inertiajs/vue3'
import { inject, onMounted, ref } from 'vue'
import axios from 'axios'
import { resportError } from '@/Helpers/Sentry'
import { handleError } from '@/Helpers/Helpers'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}

const wallet = inject('wallet')
const emitter = inject('emitter')

const props = defineProps({
    collection: Object
})

const formReveal = useForm({
    delay: false,
    password: '',
    passwordConfirm: '',
    name: '',
    description: '',
    image: ''
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
let buttonLoading = ref(false)
let validCollection = ref(true)
let contract = false

onMounted(async () => {
    contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)

    try {
        await setCollectionImages()
        await setRevealBatches()
    } catch(error) {
        emitter.emit('new-message', {type: 'error', message: handleError(error)})
    }
})

const setCollectionImages = async () => {
    try {
        const data = await getCollectionData(contract, props.collection.type, true, 8)

        // Collection
        collectionData.value.totalSupply = data.totalSupply
        collectionData.value.maxTotalSupply = data.totalSupply
        collectionData.value.totalClaimedSupply = data.totalClaimedSupply
        collectionData.value.totalRatioSupply = data.totalRatioSupply
        collectionData.value.nfts = data.nfts

        validCollection.value = collectionData.value.nfts.length > 0 ? true : false
        emitter.emit('set-tab-status', {tab: 'collection', status: collectionData.value.nfts.length > 0 ? 1 : 0})
    } catch(error) {
        emitter.emit('new-message', {type: 'error', message: handleError(error)})
    }
}

const setRevealBatches = async () => {
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
const updateCollection = async (e) => {
    if (formReveal.delay) {
        // Validate form
        let error = false
        if (formReveal.password == '') {
            error = 'Placeholder password is not valid'
        } else if (formReveal.password != formReveal.passwordConfirm) {
            error = 'Reveal password does not match'
        } else if (formReveal.name == '') {
            error = 'Placeholder name is not valid'
        } else if (formReveal.description == '') {
            error = 'Placeholder description is not valid'
        } else if (formReveal.image == '') {
            error = 'Placeholder image is not valid'
        }
        if (error) {
            messages.value.push({type: 'error', message: error})
            return
        }
    }

    emitter.emit('set-transaction', 'Updating collection')
    buttonLoading.value = true

    try {
        if (formReveal.delay) {
            await contract.revealer.createDelayedRevealBatch({
                    name: formReveal.name,
                    description: formReveal.description,
                    image: formReveal.image
                },
                collectionData.value.metadata,
                formReveal.password,
            )
            setRevealBatches()
        } else {
            await contract.createBatch(collectionData.value.metadata)
        }

        document.getElementById('image-collection').value = null
        await setCollectionImages()
        collectionData.value.previews = []

        emitter.emit('new-message', {type: 'success', message: 'NFTs added to the collection!'})
    } catch(error) {
        emitter.emit('new-message', {type: 'error', message: handleError(error)})
    }

    emitter.emit('set-transaction', false)
    buttonLoading.value = false    
}
const updateRevealBatch = async () => {
    emitter.emit('set-transaction', 'Updating reveal settings')
    buttonLoading.value = true

    try {
        await contract.revealer.reveal(collectionData.value.batchId, collectionData.value.password);
        await setCollectionImages()

        emitter.emit('new-message', {type: 'success', message: 'NFTs revealed'})
        setRevealBatches(contract)
    } catch(error) {
        emitter.emit('new-message', {type: 'error', message: handleError(error)})
    }

    emitter.emit('set-transaction', false)
    buttonLoading.value = false    
}
const uploadCollection = async (event) => {
    var files = event.target.files
    var metadata = await prepareFiles(files)

    if (metadata.status == 'error') {
        emitter.emit('new-message', {type: 'error', message: metadata.message})
        return;
    }

    collectionData.value.metadata = metadata.data
    collectionData.value.previews = collectionData.value.metadata.slice(0, 8)

    emitter.emit('new-message', {type: 'success', message: 'NFTs received'})
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
const setPlaceholderImage = (e) => {
    let files = e.target.files
    let file = files[0]

    if(validFileType(file)) {
        formReveal.image = file
    }
}
</script>
<template>
    <Box class="mb-4" title="Add your collection files" documentation="https://docs.mintpad.co/written-tutorials/upload-your-artwork">
        <BoxContent>
            <p>Upload your NFT collection. If you have not yet generated your NFT collection, use our free <Hyperlink element="a" class="text-sm" href="https://generator.mintpad.co" target="_blank">NFT generator</Hyperlink> to generate your collection.</p>
            <p class="mb-4"><Hyperlink element="a" href="/examples/demo-collection.zip">Download a demo collection.</Hyperlink></p>

            <label class="block text-mintpad-300 mb-4">
                <span class="sr-only">Choose Files</span>
                <InputFile @change="uploadCollection" id="image-collection" accept="application/json image/jpeg, image/png, image/jpg, image/gif" directory webkitdirectory mozdirectory multiple/>
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
                <p class="mb-4">
                    <Checkbox id="settings-phases" class="align-middle" type="checkbox" value="1" v-model="formReveal.delay" />
                    <Label for="settings-phases" class="ml-2 mt-1" info="Whether the collectors will immediately see the final NFT when they complete the minting or at a later time">Delayed reveal</Label>
                </p>
                <div v-if="formReveal.delay">
                    <p class="mb-4">Collectors will mint your placeholder image so you can reveal it at a later time.</p>
                    <div class="flex px-6 py-2 mb-4 rounded-md bg-white dark:bg-mintpad-800 border dark:border-primary-600 border-primary-200" role="alert">
                        <i class="fa-solid fa-circle-check text-xl text-primary-600 align-middle"></i>
                        <div class="ml-3 text-sm text-mintpad-700 dark:text-white">You will need this password to reveal your NFTs. Please store it somewhere safe.</div>
                    </div>

                    <div class="w-full flex flex-wrap">
                        <div class="basis-full sm:basis-1/2 sm:pr-2">
                            <Label for="reveal-password" value="Password" class="relative" />
                            <Input id="reveal-password" class="mb-4" type="password" v-model="formReveal.password" autocomplete="new-reveal-password"/>
                        </div>
                        <div class="basis-full sm:basis-1/2 sm:pl-2">
                            <Label for="reveal-confirm-password" value="Confirm password" />
                            <Input id="reveal-confirm-password" class="mb-4" type="password" v-model="formReveal.passwordConfirm" autocomplete="new-reveal-password-confirm"/>
                        </div>
                        <div class="basis-full">
                            <Label for="reveal-name" value="Placeholder collection name" class="relative" info="This is the placeholder name of your NFT collection." />
                            <Input id="reveal-name" class="mb-4" type="text" v-model="formReveal.name" />
                        </div>
                        <div class="basis-full">
                            <Label for="reveal-description" value="Placeholder collection description" info="This should be a short placeholder description of your collection. This is displayed in the collectors wallet until it will be revealed." />
                            <Textarea id="reveal-description" class="w-full mb-2" v-model="formReveal.description"></Textarea>
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
                <p v-else>Total minted {{ collectionData.totalRatioSupply }}% ({{ collectionData.totalClaimedSupply}}/{{ collectionData.totalSupply }})</p>
                <div class="grid grid-cols-4 mt-2">
                    <div class="p-1 text-center text-sm" v-for="nft in collectionData.nfts">
                        <img class="w-full max-w-max transition-all duration-500 rounded-md" :src="nft.metadata.image" />
                    </div>
                </div> 
            </div> 
        </BoxContent>
    </Box>

    <Box title="Reveal your NFTs">
        <BoxContent>
            <div v-if="Object.keys(collectionData.batches).length" class="mb-4">
                <div class="basis-full">
                    <Label value="Batch name" class="relative" info="Which batch of NFTs you want to reveal." />
                    <Select class="!w-full mb-4" v-model="collectionData.batchId" :options="collectionData.batches"></Select>
                </div>
                <div>
                    <Label value="Password" class="relative" info="Password that was entered while creating this batch." />
                    <Input class="w-full" type="password" v-model="collectionData.password" autocomplete="reveal-password"/>
                </div>
            </div>
            <p v-else class="mb-4">You don't have any NFTs to reveal</p>

            <span class="inline-block" content="This action will trigger a transaction" v-tippy>
                <Button href="#" @click.prevent="updateRevealBatch" :loading="buttonLoading" :disabled="!Object.keys(collectionData.batches).length">Reveal NFTs</Button>
            </span>
        </BoxContent>
    </Box>
</template>