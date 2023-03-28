<script setup>
import { ref } from 'vue'

let props = defineProps([
    'modelValue'
]);
defineEmits(['update:modelValue']);
let classes = ref([])

const startDrag = () => {
    classes.value = ['!border-mintpad-300']
}
const stopDrag = () => {
    classes.value = []
}
</script>
<template>
    <input 
        type="file"
        @dragenter="startDrag" 
        @dragleave="stopDrag"
        @input="$emit('update:modelValue', $event.target.files[0])" 
        :class="classes"
        class="inline-block p-6 border text-sm border-mintpad-200 dark:border-mintpad-400 border-dashed rounded-md w-full file:mr-2 file:px-4 file:py-2.5 file:bg-mintpad-200 file:text-mintpad-300 hover:text-mintpad-400 file:hover:text-black file:rounded-md file:text-sm file:text-center file:border-0" 
    />
</template>
