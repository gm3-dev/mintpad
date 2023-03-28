<script setup>
import Logo from './Logo.vue'

</script>
<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 px-6 sm:pt-0">
        <div class="flex items-center"> 
            <Logo />
        </div>

        <div class="w-full sm:max-w-md mt-10 p-6 sm:p-20 bg-white dark:bg-mintpad-800 dark:border dark:border-mintpad-900 overflow-hidden rounded-md">
            <slot />
        </div>
    </div>
</template>