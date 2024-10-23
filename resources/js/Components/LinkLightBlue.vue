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
    <component :is="element" :href="href" class="inline-block font-medium relative px-3 xl:px-10 py-2 text-xs rounded-full bg-blue-100 dark:bg-mintpad-700 text-primary-600 dark:text-white box-border border border-primary-600 disabled:text-mintpad-400 active:bg-primary-100 active:dark:bg-mintpad-700 focus:outline-none focus:border-mintpad-200 disabled:opacity-25 transition ease-in-out duration-150">
        <slot />
    </component>
</template>