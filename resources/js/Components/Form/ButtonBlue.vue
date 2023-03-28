<script setup>
</script>
<template>
    <button type="submit" class="inline-block font-medium relative px-3 xl:px-10 py-2.5 text-xs rounded-md bg-primary-100 dark:bg-mintpad-700 text-primary-600 dark:text-white box-border border border-primary-600 disabled:text-mintpad-400 active:bg-primary-100 active:dark:bg-mintpad-700 focus:outline-none focus:border-mintpad-200 disabled:opacity-25 transition ease-in-out duration-150">
        <i class="absolute left-3 text-lg fa-solid fa-spinner animate-spin" style="display: none;"></i>
        <slot />
    </button>
</template>