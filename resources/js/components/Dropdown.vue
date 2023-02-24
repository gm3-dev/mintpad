<script setup>
import { ref } from 'vue';

defineProps({
    refname: String
})

let open = ref(false)

function toggle() {
    open.value = !open.value
}
function hide() {
    open.value = false
}
</script>
<template>
    <div class="relative">
        <button :ref="refname" @click="toggle()" class="hover:cursor-pointer h-8">
            <slot name="button"></slot>
        </button>

        <div v-show="open" v-closable="{exclude: [this.refname], handler: 'hide'}" class="absolute z-50 mt-2 w-48 rounded-md origin-top-right right-0 dark:border dark:border-mintpad-900">
            <div class="rounded-md ring-1 ring-black ring-opacity-5 bg-white dark:bg-mintpad-800">
                <slot name="links"></slot>  
            </div>
        </div>
    </div>
</template>