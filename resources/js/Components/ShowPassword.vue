<script setup>
import { ref } from 'vue'

let showPassword = ref(false)

const toggleShowPassword = (state) => {
    showPassword.value = state
}
</script>
<template>
    <span class="inline-block w-full relative">
        <a v-if="!showPassword" href="#" class="absolute top-[8px] right-3" @click.prevent="toggleShowPassword(true)" tabindex="-1"><i class="fas fa-eye-slash"></i></a>
        <a v-else href="#" class="absolute top-[8px] right-3" @click.prevent="toggleShowPassword(false)" tabindex="-1"><i class="fas fa-eye"></i></a>
        <slot :type="showPassword ? 'text' : 'password'" />
    </span>
</template>