<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps(['modelValue']);
defineEmits(['update:modelValue']);

const input = ref(null);
const showError = ref(false);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="inline-block w-full mb-4 rounded-md px-3 py-2 font-regular text-sm text-mintpad-700 dark:text-white bg-primary-100 border border-primary-200 dark:border-mintpad-900 placeholder:text-gray-400 focus:ring-0 focus:border-primary-600 dark:focus:border-gray-600 dark:bg-mintpad-500 disabled:text-mintpad-300 disabled:border-primary-100 dark:disabled:text-mintpad-300 dark:disabled:bg-mintpad-500"
        :class="{'!border-red-600': showError}"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
