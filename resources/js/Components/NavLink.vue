<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    active: Boolean,
    element: {
        default: Link
    },
    href: String
})
</script>
<template>
    <component 
        :is="element" 
        :href="href" 
        :class="{'border-b border-primary-600': active}" 
        class="inline-flex items-center px-1 pt-1 text-lg leading-5 font-medium text-mintpad-400 dark:text-gray-200 hover:text-mintpad-700 hover:border-b hover:border-primary-600 dark:hover:text-white transition duration-150 ease-in-out"
    >
       <slot />
    </component>
</template>