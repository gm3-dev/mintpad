<script setup>
const props = defineProps({
    label: String,
    complete: Number
})
</script>
<template>
    <a v-if="complete === 2" href="#" class="inline-block w-full sm:w-auto mx-0 sm:mx-3 px-6 py-1.5 text-xs border border-mintpad-2400 bg-mintpad-200 dark:border-0  dark:bg-mintpad-700 rounded-full">
        <i class="inline-block fa-solid fa-spinner animate-spin text-base mr-1 align-middle"></i> <span class="align-middle">{{ label }}</span>
    </a>    
    <a v-else-if="complete === 1" href="#" class="inline-block w-full sm:w-auto mx-0 sm:mx-3 px-6 py-1.5 text-xs border border-green-600 bg-green-100 text-green-600 dark:text-green-600 dark:border-0 dark:bg-[#0F391D] rounded-full">
        <i class="inline-block fa-solid fa-check text-base mr-1 align-middle"></i> <span class="align-middle">{{ label }}</span>
    </a>
    <a v-else href="#" class="inline-block w-full sm:w-auto mx-0 sm:mx-3 px-6 py-1.5 text-xs border border-orange-400 bg-orange-100 text-orange-400 dark:border-0 dark:bg-[#413419] dark:text-amber-500 rounded-full">
        <i class="inline-block fa-solid fa-circle-exclamation text-base mr-1 align-middle"></i> <span class="align-middle">{{ label }}</span>
    </a>
</template>