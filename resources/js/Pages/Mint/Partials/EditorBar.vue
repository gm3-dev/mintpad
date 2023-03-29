<script setup>
import ButtonBlue from '@/Components/Form/ButtonBlue.vue'
import ButtonGray from '@/Components/Form/ButtonGray.vue'
import { objectToRgba, setStyling } from '@/Helpers/Helpers'
import { ref, computed, watch, toRaw, onMounted, inject } from 'vue'
import { Chrome } from '@ckpack/vue-color'
import Modal from '@/Components/Modal.vue'
import InputFile from '@/Components/Form/InputFile.vue'
import Button from '@/Components/Form/Button.vue'
import axios from 'axios'
import $ from 'jquery'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}

const props = defineProps({
    editMode: Boolean,
    collectionData: Object
})
let colorpicker = ref({
    primary: {
        show: false,
        color: toRaw(props.collectionData.theme.primary),
    }
})
let showModal = ref(false)
let showTutorialModal = ref(false)
let backgroundLoading = ref(false)
const emitter = inject('emitter')

onMounted(() => {
    document.addEventListener("mouseup", e => {
        let target = $(e.target)[0].className
        let closeColorPicker = !target.startsWith('vc-')
        if (closeColorPicker) {
            colorpicker.value.primary.show = false
        }
    })
})

const primaryColor = computed(() => {
    return colorpicker.value.primary.color;
})

watch(primaryColor, (color) => {
    props.collectionData.theme.primary = color.rgba ? color.rgba : color
    setStyling(props.collectionData)
})

const toggleColorpicker = (target) => {
    colorpicker.value[target].show = !colorpicker.value[target].show
}

const addBackground = () => {
    showModal.value = true
}

const uploadBackground = (e) => {
    backgroundLoading.value = true
    var files = e.target.files
    var formData = new FormData()
    formData.append('resource', files[0])
    formData.append('name', 'background')

    axios.post(route('resources.upload', props.collectionData.id), formData)
    .then((response) => {
        props.collectionData.background = response.data.url
        backgroundLoading.value = false
    }).catch((error) => {
        if (error.response.data.errors != undefined) {
            emitter.emit('new-message', {type: 'error', message: error.response.data.errors.background[0]})
        } else {
            emitter.emit('new-message', {type: 'error', message: 'Something went wrong, please try again.'})
        }
    });
}

const deleteBackground = () => {
    if (confirm("Are you sure you want to delete this background?") == true) {
        var data = {data: {name: 'background'}}
        axios.delete(route('resources.delete', props.collectionData.id), data)
        .then((response) => {
            props.collectionData.background = false
        }).catch((error) => {
            emitter.emit('new-message', {type: 'error', message: 'Something went wrong, please try again.'})
        });
    }
}

const updateMintSettings = async () => {
    var data = {
        buttons: props.collectionData.buttons,
        theme: {mint: props.collectionData.theme}
    }

    await axios.put('/collections/'+props.collectionData.id, data)
    .catch((error) => {
        if (error.response.status == 422) {
            emitter.emit('new-message', {type: 'error', message: error.response.data.message})
        }
    })
    .then((response) => {
        if (response) {
            emitter.emit('new-message', {type: 'success', message: 'Mint settings updated'})
        }
    })
}
</script>
<template>
    <div v-if="editMode" class="relative sm:fixed z-40 h-auto sm:h-14 left-0 top-0 p-2 w-full bg-white dark:bg-mintpad-500 border-b border-mintpad-200 dark:border-mintpad-900">
        <div class="max-w-7xl mx-auto px-6 flex flex-wrap gap-4 items-center">
            <div id="color-picker-container" class="grow w-full sm:w-auto text-mintpad-700 relative">
                <a href="#" @click.prevent="toggleColorpicker('primary')" class="vc-open-color-picker inline-block align-middle rounded-md w-7 h-7 border border-gray-200" :style="{backgroundColor: objectToRgba(colorpicker.primary.color, 1)}"></a><span class="text-sm ml-4 align-middle dark:text-mintpad-200">Primary color</span>
                <div v-if="colorpicker.primary.show" class="absolute top-11 left-0">
                    <Chrome v-model="colorpicker.primary.color" :disable-alpha="true"></Chrome>
                </div>
            </div>

            <ButtonGray class="px-2 sm:!px-4 text-center" @click.prevent="addBackground">Change background</ButtonGray>
            <Button class="px-2 sm:!px-4 text-center" @click.prevent="updateMintSettings">Publish changes</Button>
            <ButtonBlue class="align-middle !rounded-full px-2 sm:!px-5 !py-1 !text-xs !leading-6" @click.prevent="showTutorialModal = true"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle text-xs">Watch tutorial</span></ButtonBlue>
        </div>
    </div>
    <Modal :show="showTutorialModal" :title="'Tutorial video'" @close="showTutorialModal = false">
        <div class="w-full text-center"><iframe class="inline-block" width="560" height="315" src="https://www.youtube.com/embed/Qn2-nY0vZfQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
    </Modal>
    <Modal :show="showModal" title="Change background" @close="showModal = false">
        <form>
            <div v-if="backgroundLoading" class="w-full text-center mb-4">
                <i class="fa-solid fa-cloud-arrow-up animate-bounce mr-2 text-lg"></i> uploading...
            </div>
            <div v-else-if="collectionData.background" class="text-center mb-4">
                <img :src="collectionData.background" class="inline-block w-auto max-h-40" />
            </div>
            <label v-else for="upload-background" class="block mb-4">
                <p class="font-regular text-sm mb-1">Uploads are restricted to 5120 KB and jpg, jpeg and png.</p>
                <p class="font-regular text-sm mb-2">This background will be resized to an image with a width of 2560 pixels.</p>
                <span class="sr-only">Choose File</span>
                <InputFile id="upload-background" @change="uploadBackground" accept="image/jpeg, image/png, image/jpg" />
            </label>
            <div class="mt-4">
                <span class="inline-block" content="Delete background" v-tippy>
                    <ButtonGray href="#" class="!px-4" @click.prevent="deleteBackground"><i class="fas fa-trash-alt"></i></ButtonGray>
                </span>
                <span class="float-right inline-block" content="Save" v-tippy>
                    <Button href="#" class="!px-4" @click.prevent="showModal = false">Save</Button>
                </span>
            </div>
        </form>
    </Modal>
</template>