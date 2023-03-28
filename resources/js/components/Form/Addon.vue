<script setup>
defineProps({
    position: String,
    content: String
})
</script>
<template>
    <span class="relative w-full inline-flex">
        <span v-if="position == 'left'" class="inline-flex mb-4 rounded-l-md font-regular text-sm text-mintpad-700 dark:text-white bg-primary-100 dark:bg-mintpad-500 border border-r-0 border-primary-200 dark:border-mintpad-900">
            <span class="inline-block px-3 my-2 border-r border-primary-200 dark:border-mintpad-900">{{ content }}</span>
        </span>
        <slot />
        <span v-if="position == 'right'" class="inline-flex mb-4 rounded-r-md font-regular text-sm text-mintpad-700 dark:text-white bg-primary-100 dark:bg-mintpad-500 border border-l-0 border-primary-200 dark:border-mintpad-900">
            <span class="inline-block px-3 my-2 border-l border-primary-200 dark:border-mintpad-900">{{ content }}</span>
        </span>
    </span>
</template>