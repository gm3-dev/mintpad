<script setup>
import { ref } from 'vue'


let className = ref('fa-copy')
const props = defineProps({
    text: String
})
const copy = () => {
    navigator.clipboard.writeText(props.text)

    className.value = 'fa-check text-green'
    setTimeout(() => {
        className.value = 'fa-copy'
    }, 1000)
}
</script>
<template>
    <a href="#" @click.prevent="copy" content="Copy address" v-tippy><i class="fas mr-2 text-mintpad-700 dark:text-white" :class="className"></i></a>
</template>