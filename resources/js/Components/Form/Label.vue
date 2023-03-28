<script setup>
defineProps({
    value: String,
    info: String
})
</script>
<template>
    <label class="inline-block text-xs font-medium mb-1 ml-1 text-mintpad-400 dark:text-gray-400">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span v-if="info" class="inline-block ml-2" v-tippy="info">
            <i class="fas fa-question-circle text-sm text-mintpad-700 dark:text-gray-200"></i>
        </span> 
    </label>
</template>
