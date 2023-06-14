<script setup>
import { onMounted } from 'vue'
const emit = defineEmits([
    'close'
])
const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    showClose: {
        type: Boolean,
        default: true
    },
    title: {
        type: String,
        default: 'Default title'
    }
})
onMounted(() => {
    window.addEventListener("keyup", e => {
        if (e.key == 'Escape' && props.show == true) {
            emit('close')
        }
    });
})
</script>
<template>
    <div v-if="show" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed z-40 inset-0 bg-gray-200 dark:bg-mintpad-500 bg-opacity-75 dark:bg-opacity-50 transition-opacity"></div>
        <div class="fixed z-50 inset-0 overflow-y-auto">
            <div class="flex items-end sm:items-center justify-center min-h-full p-4 sm:p-0">
                <div class="relative bg-white dark:bg-mintpad-800 dark:border dark:border-mintpad-900 rounded-md text-left transform transition-all sm:my-8 sm:max-w-3xl sm:w-full">
                    <div v-if="title" class="border-b border-mintpad-200 dark:border-mintpad-900 font-jpegdev px-10 py-4">
                        <h2 v-html="title" class="!mb-0"></h2>
                    </div>
                    <div class="bg-white dark:bg-mintpad-800 px-10 py-6">
                        <a v-if="showClose" href="#" class="absolute right-4 top-3 text-xs font-medium text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="$emit('close')">close</a>
                        <div>
                            <slot />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>