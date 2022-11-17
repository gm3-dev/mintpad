<template>
    <span v-if="complete === true" class="inline-block mt-1 px-2 border-2 border-green-700 dark:border-green-600 rounded-md">
        <span class="inline-block text-green-700 dark:text-green-600 text-xs"><i class="inline-block fa-solid fa-check text-base mr-1"></i> Complete</span>
    </span>
    <span v-else class="inline-block mt-1 px-2 border-2 border-orange-400 rounded-md">
        <span class="inline-block text-orange-400 text-xs mr-2"><i class="inline-block fa-solid fa-exclamation text-base mr-1"></i> Incomplete</span>
    </span>
</template>

<script>
    export default {
        props: ['complete'],
        data() {
            return {}
        },
        methods: {
        }
    }
</script>