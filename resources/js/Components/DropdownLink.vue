<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    element: {
        default: Link
    },
    href: String,
    target: String
})
</script>
<template>
    <component :is="element" :href="href" :target="target" class="block px-4 py-2 font-regular text-sm leading-5 text-mintpad-400 dark:text-white hover:text-mintpad-300 dark:hover:text-primary-600 transition duration-150 ease-in-out">
        <slot></slot>
    </component>
</template>