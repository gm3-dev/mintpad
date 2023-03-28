<script setup>
</script>
<template>
    <button type="submit" class="inline-block font-medium px-4 lg:px-10 py-2.5 text-xs bg-mintpad-200 dark:bg-mintpad-700 text-mintpad-700 dark:text-mintpad-200 hover:text-mintpad-600 disabled:text-mintpad-400 border border-transparent dark:hover:border-mintpad-400 rounded-md text-sm text-center disabled:opacity-25 transition ease-in-out duration-150">
        <i class="absolute left-3 text-lg fa-solid fa-spinner animate-spin" style="display: none;"></i>
        <slot />
    </button>
</template>