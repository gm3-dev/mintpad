<script setup>
const props = defineProps({
    options: {
        type: Object,
        default: {}
    },
    selected: {
        type: String,
        default: ''
    },
    optgroup: Boolean,
    modelValue: [Object, String, Number]
})
</script>
<template>
    <select :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" class="mb-4 rounded-md p-6 w-32 px-3 py-2 text-sm font-regular text-mintpad-700 dark:text-white bg-primary-100 border-1 border-primary-200 dark:border-mintpad-900 focus:ring-0 focus:border-primary-600 dark:focus:border-gray-600 dark:bg-mintpad-500 disabled:bg-mintpad-200 disabled:text-mintpad-200">
        <option v-for="(label, value, index) in options" :value="value">{{ label }}</option>
    </select>
</template>