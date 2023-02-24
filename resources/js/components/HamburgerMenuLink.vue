<script setup>
import { computed } from 'vue';

const props = defineProps({
    href: String,
    active: String,
    target: String
})

const classList = computed(() => {
    if (props.active) {
        return 'block pl-3 pr-4 py-2 border-l-4 border-primary-600 text-base font-medium text-primary-600 dark:text-white bg-primary-100 dark:bg-mintpad-700 focus:outline-none focus:text-primary-600 focus:bg-primary-100 focus:border-primary-600 transition duration-150 ease-in-out'
    } else {
        return 'block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-mintpad-400 dark:text-white hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out'
    }
})
</script>
<template>
    <a :href="href" v-bind:class="classList" :target="target">
        <slot></slot>
    </a>
</template>