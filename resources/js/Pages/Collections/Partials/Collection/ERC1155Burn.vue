<script setup>
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import InputFile from '@/Components/Form/InputFile.vue'
import Label from '@/Components/Form/Label.vue'
import Textarea from '@/Components/Form/Textarea.vue'
import { getSmartContractFromSigner } from '@/Helpers/Thirdweb'
import { useForm } from '@inertiajs/vue3'
import { inject, onMounted, ref } from 'vue'
import axios from 'axios'
import { fileIsImage, fileIsVideo, getAllowedNFTTypes, handleError } from '@/Helpers/Helpers'
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
    base: {
        name: '',
        description: '',
        file: { src: '' }
    },
    burn: {
        name: '',
        description: '',
        file: { src: '' }
    }
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
        if (NFTData[0] !== undefined && NFTData[1] !== undefined) {
            form.base.name = NFTData[0].metadata.name ?? ''
            form.base.description = NFTData[0].metadata.description ?? ''
            form.base.file.src = NFTData[0].metadata.image ?? ''

            form.burn.name = NFTData[1].metadata.name ?? ''
            form.burn.description = NFTData[1].metadata.description ?? ''
            form.burn.file.src = NFTData[1].metadata.image ?? ''

            validCollection.value = true
            emitter.emit('set-tab-status', {tab: 'collection', status: 1})
        } else {
            validCollection.value = false   
            emitter.emit('set-tab-status', {tab: 'collection', status: 0})
        }
    } catch(error) {
        emitter.emit('new-message', {type: 'error', message: handleError(error)})
    }
}

const uploadNFT = async (event, target) => {
    var file = event.target.files[0]
    file.src = URL.createObjectURL(file)
    form[target].file = file
}

const updateCollection = async (e) => {
    if (! validateForm()) {
        return
    }

    emitter.emit('set-transaction', 'Updating collection')
    buttonLoading.value = true

    try {
        let metadata = [{
            name: form.base.name,
            description: form.base.description,
            image: form.base.file
        },{
            name: form.burn.name,
            description: form.burn.description,
            image: form.burn.file
        }]
        await contract.createBatch(metadata)
        document.getElementById('image-1').value = null
        document.getElementById('image-2').value = null
        await setCollectionImages()

        emitter.emit('new-message', {type: 'success', message: 'NFTs added to the collection!'})
    } catch(error) {
        emitter.emit('new-message', {type: 'error', message: handleError(error)})
    }

    emitter.emit('set-transaction', false)
    buttonLoading.value = false
}

const validateForm = () => {
    const allowedNFTTypes = getAllowedNFTTypes()
    let error = false
    if (form.base.name.trim() == '' || form.burn.name.trim() == '') {
        error = 'NFT name is not valid'
    } else if (form.base.description.trim() == '' || form.burn.description.trim() == '') {
        error = 'NFT description is not valid'
    } else if (form.base.file == '' || form.burn.file == '') {
        error = 'NFT file is not valid'
    } else if(!allowedNFTTypes.includes(form.base.file.type) || !allowedNFTTypes.includes(form.burn.file.type)) {
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
                        <Label for="name" value="Name of your NFT" class="relative" info="Name of the NFT before burning." />
                        <Input type="text" id="name" class="w-full" v-model="form.base.name" autofocus />
                    </div>
                    <div class="basis-full">
                        <Label for="description" value="Description" class="relative" info="Description about the NFT before burning." />
                        <Textarea id="description" type="text" v-model="form.base.description" :rows="4" class="w-full"></Textarea>
                    </div>
                    <label class="block text-mintpad-300 mb-4">
                        <span class="sr-only">Choose Files</span>
                        <InputFile id="image-1" @change="uploadNFT($event, 'base')" accept="video/mp4, image/jpeg, image/png, image/jpg, image/gif" />
                    </label>

                    <p>Allowed file format: PNG, GIF, JPG and MP4</p>
                </div> 
            </BoxContent>
        </Box>

        <Box class="mb-4" v-if="!validCollection" title="After burn">
            <BoxContent>
                <div class="text-sm">
                    <p class="mb-4">This image will be the image people get after burning their NFT.</p>
                    
                    <div class="basis-full">
                        <Label for="name" value="Name of your NFT" class="relative" info="Name of the NFT after burning." />
                        <Input type="text" id="name" class="w-full" v-model="form.burn.name" autofocus />
                    </div>
                    <div class="basis-full">
                        <Label for="description" value="Description" class="relative" info="Description about the NFT after burning." />
                        <Textarea id="description" type="text" v-model="form.burn.description" :rows="4" class="w-full"></Textarea>
                    </div>
                    <label class="block text-mintpad-300 mb-4">
                        <span class="sr-only">Choose Files</span>
                        <InputFile id="image-2" @change="uploadNFT($event, 'burn')" accept="video/mp4, image/jpeg, image/png, image/jpg, image/gif" />
                    </label>

                    <p>Allowed file format: PNG, GIF, JPG and MP4</p>
                </div> 
            </BoxContent>
        </Box>

        <Box class="mb-4" :title="'Preview: ' + form.base.name">
            <BoxContent>
                <div class="text-sm">
                    <p class="mb-4">This image will be the image people mint.</p>
                    <img v-if="form.base.file.src && fileIsImage(form.base.file)" class="w-full max-w-max transition-all duration-500 rounded-md" :src="form.base.file.src" />
                    <video v-if="form.base.file.src && fileIsVideo(form.base.file)" class="transition-all duration-500 rounded-md" width="512" height="512" autoplay loop>
                        <source :src="form.base.file.src" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div> 
            </BoxContent>
        </Box>

        <Box class="mb-4" :title="'Preview: ' + form.burn.name">
            <BoxContent>
                <div class="text-sm">
                    <p class="mb-4">This image will be the image people get after burning their NFT.</p>
                    <img v-if="form.burn.file.src && fileIsImage(form.burn.file)" class="w-full max-w-max transition-all duration-500 rounded-md" :src="form.burn.file.src" />
                    <video v-if="form.burn.file.src && fileIsVideo(form.burn.file)" class="transition-all duration-500 rounded-md" width="512" height="512" autoplay loop>
                        <source :src="form.burn.file.src" type="video/mp4">
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