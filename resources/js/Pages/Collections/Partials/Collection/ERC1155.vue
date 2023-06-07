<script setup>
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import InputFile from '@/Components/Form/InputFile.vue'
import Label from '@/Components/Form/Label.vue'
import Textarea from '@/Components/Form/Textarea.vue'
import { resportError } from '@/Helpers/Sentry'
import { getSmartContractFromSigner } from '@/Helpers/Thirdweb'
import { getAllowedNFTTypes, fileIsImage, fileIsVideo } from '@/Helpers/Helpers'
import { useForm } from '@inertiajs/vue3'
import { inject, onMounted, ref } from 'vue'
import axios from 'axios'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}

const wallet = inject('wallet')
const emitter = inject('emitter')

const props = defineProps({
    collection: Object
})

const form = useForm({
    name: '',
    description: '',
    file: { src: '' }
})

let buttonLoading = ref(false)
let validCollection = ref(true)
let contract = false

onMounted(async () => {
    contract = await getSmartContractFromSigner(wallet.value.signer, props.collection.chain_id, props.collection.address, props.collection.type)

    await setCollectionImages()
})

const setCollectionImages = async () => {
    try {
        let NFTData = await contract.getAll({count: 2})
        if (NFTData[0] !== undefined) {
            form.name = NFTData[0].metadata.name ?? ''
            form.description = NFTData[0].metadata.description ?? ''
            form.file.src = NFTData[0].metadata.image ?? ''

            validCollection.value = true
            emitter.emit('set-tab-status', {tab: 'collection', status: 1})
        } else {
            validCollection.value = false   
            emitter.emit('set-tab-status', {tab: 'collection', status: 0})
        }
    } catch(error) {
        console.log('error', error)
    }
}

const uploadNFT = async (event) => {
    var file = event.target.files[0]
    file.src = URL.createObjectURL(file)
    form.file = file
}

const updateCollection = async (e) => {
    if (! validateForm()) {
        return
    }

    emitter.emit('set-transaction', 'Updating collection')
    buttonLoading.value = true

    try {
        let metadata = [{
            name: form.name,
            description: form.description,
            image: form.file
        }]
        await contract.createBatch(metadata)
        await setCollectionImages()

        document.getElementById('image-1').value = null
        
        if (form.file && fileIsImage(form.file.type)) {
            await axios.post('/collections/'+props.collection.id+'/thumb', {url: form.file.src}).then((response) => {
                //
            })
        }

        validCollection.value = true
        emitter.emit('new-message', {type: 'success', message: 'NFTs added to the collection!'})
    } catch(error) {
        console.log('error', error)
        resportError(error)
        emitter.emit('new-message', {type: 'error', message: 'Something went wrong, please try again.'})
    }

    emitter.emit('set-transaction', false)
    buttonLoading.value = false
}

const validateForm = () => {
    const allowedNFTFormats = getAllowedNFTTypes()
    let error = false
    if (form.name.trim() == '') {
        error = 'NFT name is not valid'
    } else if (form.description.trim() == '') {
        error = 'NFT description is not valid'
    } else if (form.file == '') {
        error = 'NFT file is not valid'
    } else if(!allowedNFTFormats.includes(form.file.type)) {
        error = 'This NFT file type is not allowed'
    }

    if (error) {
        emitter.emit('new-message', {type: 'error', message: error})
        return false
    } else {
        return true
    }
}
</script>
<template>
    <Box class="mb-4" title="Add images to your open edition" documentation="https://docs.mintpad.co/written-tutorials/upload-your-artwork">
        <BoxContent>
            <div class="text-sm">
                <p>In this step, you will upload the images that your community will mint, and the image they will receive when they burn your collection.</p>
            </div> 
        </BoxContent>
    </Box>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <Box class="mb-4" v-if="!validCollection" title="Before burn">
            <BoxContent>
                <div class="text-sm">
                    <p class="mb-4">This image will be the image people mint.</p>

                    <div class="basis-full">
                        <Label for="name" value="Name of your NFT" class="relative" info="This is the name of your NFT." />
                        <Input type="text" id="name" class="w-full" v-model="form.name" autofocus />
                    </div>
                    <div class="basis-full">
                        <Label for="description" value="Description" class="relative" info="You can write a short description about your NFT collection here." />
                        <Textarea id="description" type="text" v-model="form.description" :rows="4" class="w-full"></Textarea>
                    </div>
                    <label class="block text-mintpad-300 mb-4">
                        <span class="sr-only">Choose Files</span>
                        <InputFile id="image-1" @change="uploadNFT($event)" accept="video/mp4, image/jpeg, image/png, image/jpg, image/gif" />
                    </label>

                    <p>Allowed file format: PNG, GIF, JPG and MP4</p>
                </div> 
            </BoxContent>
        </Box>

        <Box class="mb-4" :title="'Preview: ' + form.name">
            <BoxContent>
                <div class="text-sm">
                    <p class="mb-4">This image will be the image people mint.</p>
                    <img v-if="form.file.src && fileIsImage(form.file.type)" class="w-full max-w-max transition-all duration-500 rounded-md" :src="form.file.src" />
                    <video v-if="form.file.src && fileIsVideo(form.file.type)" width="512" height="512" autoplay loop>
                        <source :src="form.file.src" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div> 
            </BoxContent>
        </Box>

        <div v-if="!validCollection" class="w-full mb-8">
            <span class="inline-block" content="This action will trigger a transaction" v-tippy>
                <Button href="#" @click.prevent="updateCollection" :disabled="buttonLoading" :loading="buttonLoading">Update collection</Button>
            </span>
        </div>
    </div>
</template>