<script setup>
import { ref } from 'vue';

const props = defineProps({
    width: {
        type: String,
        default: 'w-48'
    },
    id: String
})

let open = ref(false)

const toggle = () => {
    open.value = !open.value
}
const hide = () => {
    open.value = false
}
</script>
<template>
    <div class="relative">
        <button :id="id" @click="toggle()" class="hover:cursor-pointer h-8">
            <slot name="button"></slot>
        </button>

        <div v-show="open" v-closable="{exclude: id, handler: hide}" class="absolute z-50 mt-3 rounded-md origin-top-right right-0 dark:border dark:border-mintpad-900" :class="width">
            <div class="w-3 h-3 absolute rotate-45 top-[-6px] right-5 rounded-xs border-t border-l border-mintpad-200 dark:border-mintpad-900 bg-white dark:bg-mintpad-800"></div>
            <div class="rounded-md border border-mintpad-200 dark:border-mintpad-900 bg-white dark:bg-mintpad-800">
                <slot name="links"></slot>  
            </div>
        </div>
    </div>
</template>