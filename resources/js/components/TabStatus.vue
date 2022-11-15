<template>
    <span v-if="completed === true" class="inline-block text-green-700 text-xs"><i class="inline-block fa-solid fa-check text-base mr-1"></i> Complete</span>
    <span v-else class="inline-block text-orange-400 text-xs mr-2"><i class="inline-block fa-solid fa-exclamation text-base mr-1"></i> Incomplete</span>
</template>

<script>
    export default {
        props: ['completed'],
        data() {
            return {}
        },
        methods: {
        }
    }
</script>