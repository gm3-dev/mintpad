<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    element: {
        default: Link
    },
    href: String
})
</script>
<template>
    <component :is="element" :href="href" class="text-primary-600 hover:text-primary-700 hover:underline active:bg-primary-700 transition ease-in-out duration-150">
       <slot />
    </component>
</template>