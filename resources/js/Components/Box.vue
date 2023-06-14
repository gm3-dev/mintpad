<script setup>
import Modal from '@/Components/Modal.vue'
import { ref } from 'vue'
import LinkLightBlue from './LinkLightBlue.vue'

defineProps({
    title: String,
    documentation: String
})

let showModal = ref(false)
const toggleModal = (state) => {
    showModal.value = state
}
</script>
<template>
    <div class="mb-4 border border-mintpad-200 dark:border-mintpad-900 bg-white dark:bg-mintpad-800 rounded-md">
        <div v-if="title" class="relative border-b border-mintpad-200 dark:border-mintpad-900 font-jpegdev px-8 py-3 mint-border-dark">
            <h2 class="!mb-0 mint-text-dark">{{ title }}
                <LinkLightBlue element="a" v-if="documentation" class="float-right ml-4 !rounded-full !px-5 !py-1 !text-xs" :href="documentation"><span class="align-middle text-xs">Visit our documentation</span></LinkLightBlue>
            </h2>
            <slot name="action"></slot>
        </div>

        <slot />
        <Modal :show="showModal" :title="'Tutorial video'" @close="toggleModal(false)">
            <div class="w-full text-center"><iframe class="inline-block" width="560" height="315" :src="tutorial" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
        </Modal>
    </div>
</template>