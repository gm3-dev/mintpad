<script setup>
import ButtonBlue from '@/Components/Form/ButtonBlue.vue'
import Modal from '@/Components/Modal.vue'
import { ref } from 'vue'

defineProps({
    title: String,
    tutorial: String
})

let showModal = ref(false)
const toggleModal = (state) => {
    showModal.value = state
}
</script>
<template>
    <div class="mb-4 border border-mintpad-200 dark:border-mintpad-900 bg-white dark:bg-mintpad-800 rounded-md">
        <div v-if="title" class="relative border-b border-mintpad-200 dark:border-mintpad-900 font-jpegdev px-8 py-3 mint-border-dark">
            <h2 class="!mb-0">{{ title }}
                <ButtonBlue v-if="tutorial" class="align-middle ml-4 !rounded-full !px-5 !py-1 !text-xs" @click.prevent="toggleModal(true)"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle text-xs">Watch tutorial</span></ButtonBlue>
            </h2>
            <slot name="action"></slot>
        </div>

        <slot />
        <Modal :show="showModal" :title="'Tutorial video'" @close="toggleModal(false)">
            <div class="w-full text-center"><iframe class="inline-block" width="560" height="315" :src="tutorial" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
        </Modal>
    </div>
</template>