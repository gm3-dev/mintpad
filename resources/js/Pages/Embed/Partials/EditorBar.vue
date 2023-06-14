<script setup>
import $ from 'jquery'
import DarkMode from '@/Components/DarkMode.vue'
import Button from '@/Components/Form/Button.vue'
import ButtonBlue from '@/Components/Form/ButtonBlue.vue'
import Modal from '@/Components/Modal.vue'
import { objectToRgba, setStyling } from '@/Helpers/Helpers'
import { Chrome } from '@ckpack/vue-color'
import { ref, toRaw, onMounted, watch, computed, inject } from 'vue'
import axios from 'axios'
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
    },
    background: {
        show: false,
        color: toRaw(props.collectionData.theme.background),
    },
    phases: {
        show: false,
        color: toRaw(props.collectionData.theme.phases),
    }
})
const emitter = inject('emitter')

onMounted(() => {
    document.addEventListener("mouseup", e => {
        let target = $(e.target)[0].className
        let closeColorPicker = !target.startsWith('vc-')
        if (closeColorPicker) {
            colorpicker.value.primary.show = false
            colorpicker.value.background.show = false
            colorpicker.value.phases.show = false
        }
    })
})

const primaryColor = computed(() => {
    return colorpicker.value.primary.color;
})
const backgroundColor = computed(() => {
    return colorpicker.value.background.color;
})
const phasesColor = computed(() => {
    return colorpicker.value.phases.color;
})

watch(primaryColor, (color) => {
    props.collectionData.theme.primary = color.rgba ? color.rgba : color
    setStyling(props.collectionData)
})
watch(backgroundColor, (color) => {
    props.collectionData.theme.background = color.rgba ? color.rgba : color
    setStyling(props.collectionData)
})
watch(phasesColor, (color) => {
    props.collectionData.theme.phases = color.rgba ? color.rgba : color
    setStyling(props.collectionData)
})

const toggleColorpicker = (target) => {
    colorpicker.value[target].show = !colorpicker.value[target].show
}

const updateMintSettings = async () => {
    var data = {
        theme: {embed: props.collectionData.theme},
        settings: {embed: props.collectionData.settings},
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
                <div class="inline-block relative">
                    <a href="#" @click.prevent="toggleColorpicker('primary')" class="vc-open-color-picker inline-block align-middle rounded-md w-7 h-7 border border-gray-200" :style="{backgroundColor: objectToRgba(colorpicker.primary.color, 1)}"></a><span class="text-sm mx-4 align-middle dark:text-mintpad-200">Primary color</span>
                    <div v-if="colorpicker.primary.show" class="absolute top-11 left-0">
                        <Chrome v-model="colorpicker.primary.color" :disable-alpha="true"></Chrome>
                    </div>
                </div>
                <div class="inline-block relative">
                    <a href="#" @click.prevent="toggleColorpicker('background')" class="vc-open-color-picker inline-block align-middle rounded-md w-7 h-7 border border-gray-200" :style="{backgroundColor: objectToRgba(colorpicker.background.color, 1)}"></a><span class="text-sm mx-4 align-middle dark:text-mintpad-200">Background</span>
                    <div v-if="colorpicker.background.show" class="absolute top-11 left-0">
                        <Chrome v-model="colorpicker.background.color" :disable-alpha="true"></Chrome>
                    </div>
                </div>
                <div class="inline-block relative">
                    <a href="#" @click.prevent="toggleColorpicker('phases')" class="vc-open-color-picker inline-block align-middle rounded-md w-7 h-7 border border-gray-200" :style="{backgroundColor: objectToRgba(colorpicker.phases.color, 1)}"></a><span class="text-sm ml-4 align-middle dark:text-mintpad-200">Mint phase background</span>
                    <div v-if="colorpicker.phases.show" class="absolute top-11 left-0">
                        <Chrome v-model="colorpicker.phases.color" :disable-alpha="true"></Chrome>
                    </div>
                </div>
            </div>

            <DarkMode></DarkMode>
            <Button href="#" class="px-2 sm:!px-4 text-center" @click.prevent="updateMintSettings">Publish changes</Button>
        </div>
    </div>
</template>