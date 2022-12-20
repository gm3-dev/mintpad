<template>
    <a v-if="complete === true" href="#" class="inline-block mx-3 px-6 py-1.5 text-xs text-green-700 bg-green-200 dark:text-green-600 border border-green-700 dark:border-green-600 rounded-full">
        <i class="inline-block fa-solid fa-check text-base mr-1 align-middle"></i> <span class="align-middle">{{ label }}</span>
    </a>
    <a v-else href="#" class="inline-block mx-3 px-6 py-1.5 text-xs text-orange-400 bg-orange-100 border border-orange-400 rounded-full">
        <i class="inline-block fa-solid fa-circle-exclamation text-base mr-1 align-middle"></i> <span class="align-middle">{{ label }}</span>
    </a>
</template>

<script>
    export default {
        props: ['label', 'complete'],
        data() {
            return {}
        },
        methods: {
        }
    }
</script>