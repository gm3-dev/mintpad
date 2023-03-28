<script setup>
const props = defineProps({
    validation: [String, Boolean]
})
</script>
<template>
    <span v-if="validation" class="text-xs absolute -bottom-[2px] left-1 text-red-500">{{ validation }}</span>
</template>