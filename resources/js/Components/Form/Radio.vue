<script setup>
import { computed } from 'vue';

defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: [String, Number],
    value: {
        default: '',
    },
});

const isChecked = computed(() => {
    return props.modelValue == props.value
});
</script>

<template>
    <input
        type="radio"
        :value="value"
        :checked="isChecked"
        @change="$emit('update:modelValue', $event.target.value)"
        class="rounded-lg p-1 text-sm text-mintpad-300 border border-mintpad-200 focus:ring-0 focus:border-mintpad-200 focus:ring-primary-200 disabled:bg-mintpad-200 disabled:text-mintpad-300"
    />
</template>