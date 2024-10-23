<script setup>
import Button from '@/Components/Form/Button.vue'
import ButtonDefault from '@/Components/Form/ButtonDefault.vue'
import ButtonGray from '@/Components/Form/ButtonGray.vue'
import InputFile from '@/Components/Form/InputFile.vue'
import Modal from '@/Components/Modal.vue'
import axios from 'axios'
import { ref, inject } from 'vue'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}
const props = defineProps({
    editMode: Boolean,
    collectionData: Object
})
let showModal = ref(false)
let logoLoading = ref(false)
const emitter = inject('emitter')

const addLogo = () => {
    showModal.value = true
}

const uploadLogo = (e) => {
    logoLoading.value = true
    var files = e.target.files
    var formData = new FormData()
    formData.append('resource', files[0])
    formData.append('name', 'logo')

    axios.post(route('resources.upload', props.collectionData.id), formData)
    .then((response) => {
        props.collectionData.logo = response.data.url
        logoLoading.value = false
    }).catch((error) => {
        if (error.response.data.errors != undefined) {
            emitter.emit('new-message', {type: 'error', message: error.response.data.errors.logo[0]})
        } else {
            emitter.emit('new-message', {type: 'error', message: 'Something went wrong, please try again.'})
        }
    });
}

const deleteLogo = () => {
    if (confirm("Are you sure you want to delete this logo?") == true) {
        var data = {data: {name: 'logo'}}
        axios.delete(route('resources.delete', props.collectionData.id), data)
        .then((response) => {
            props.collectionData.logo = false
        }).catch((error) => {
            emitter.emit('new-message', {type: 'error', message: 'Something went wrong, please try again.'})
        });
    }
}
</script>
<template>
    <a v-if="editMode" href="#" @click.prevent="addLogo" class="absolute top-4 left-6">
        <img v-if="collectionData.logo" :src="collectionData.logo" class="inline-block h-full max-h-10 sm:max-h-16 md:max-h-20 max-w-10 sm:max-w-16 md:max-w-20" content="Edit logo" v-tippy="{placement: 'bottom'}" />
        <div v-else>
            <ButtonDefault href="#" class="!px-3"><i class="fa-solid fa-plus mr-2 text-lg align-middle"></i> <span class="align-middle">Add logo</span></ButtonDefault>
        </div>
    </a>
    <div v-if="!editMode" class="absolute top-4 left-6">
        <img v-if="collectionData.logo" :src="collectionData.logo" class="inline-block h-full max-h-10 sm:max-h-16 md:max-h-20 max-w-10 sm:max-w-16 md:max-w-20" />
    </div>
    
    <Modal :show="showModal" title="Edit logo" @close="showModal = false">
        <form>
            <div v-if="logoLoading" class="w-full text-center mb-4">
                <i class="fa-solid fa-cloud-arrow-up animate-bounce mr-2 text-lg"></i> uploading...
            </div>
            <div v-else-if="collectionData.logo" class="text-center mb-4">
                <img :src="collectionData.logo" class="inline-block w-auto max-h-40" />
            </div>
            <label v-else for="upload-logo" class="block mb-4">
                <p class="font-regular text-sm mb-1">Uploads are restricted to 5120 KB and jpg, jpeg and png.</p>
                <p class="font-regular text-sm mb-2">This logo will be resized to an image with a width of 400 pixels.</p>
                <span class="sr-only">Choose File</span>
                <InputFile id="upload-logo" @change="uploadLogo" accept="image/jpeg, image/png, image/jpg" />
            </label>
            <div class="mt-4">
                <span class="inline-block" content="Delete logo" v-tippy>
                    <ButtonGray href="#" class="!px-4" @click.prevent="deleteLogo"><i class="fas fa-trash-alt"></i></ButtonGray>
                </span>
                <span class="float-right inline-block" content="Save" v-tippy>
                    <Button href="#" class="!px-4" @click.prevent="showModal = false">Save</Button>
                </span>
            </div>
        </form>
    </Modal>
</template>