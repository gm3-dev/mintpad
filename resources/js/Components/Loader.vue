<script setup>
defineProps({
    loading: Boolean
})
</script>
<template>
    <div class="w-10 mx-auto pt-4 text-lg dark:text-white"><img src="/images/icon.svg" class="h-[35px] animate-bounce" />
        <div class="fixed z-40 inset-0 bg-gray-200 dark:bg-mintpad-500 bg-opacity-50 dark:bg-opacity-50 transition-opacity"></div>
    </div>
</template>