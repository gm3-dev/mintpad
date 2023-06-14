<script setup>
import Box from '@/Components/Box.vue'
import BoxRow from '@/Components/BoxRow.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import LinkBlue from '@/Components/LinkBlue.vue'
import { Head } from '@inertiajs/vue3'
import { ref, provide, onMounted } from 'vue'
import { getBlockchains } from '@/Helpers/Blockchain'

defineProps({
    collections: Object,
    last_update: String
})

let loading = ref(true)
let wallet = ref({account: false})
let validBlockchain = ref(true)
let blockchains = ref(getBlockchains())

provide('wallet', wallet)

onMounted(async () => {
    // Done loading
    loading.value = false
})
</script>
<template>
    <AuthenticatedLayout :loading="loading" :valid-blockchain="validBlockchain">
        <Head title="Upcoming" />

        <div class="text-center mb-10">
            <h1>Upcoming</h1>
        </div>
        
        <Box title="Mint phases">
            <template v-slot:action>
                <span class="absolute right-8 top-5 text-xs font-medium text-mintpad-300">Last update: {{ last_update }}</span>
            </template>
            <BoxRow class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                <div class="basis-3/5">Collection name</div>
                <div class="basis-1/5">Mint info</div>
                <div class="basis-1/5 text-right">When</div>
            </BoxRow>
            <BoxRow v-for="collection in collections" class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-3/5">{{ collection.name }}</div>
                <div class="basis-1/5">{{ collection.mint_price }} {{ blockchains[collection.chain_id].nativeCurrency.symbol }}</div>
                <div class="basis-1/5 text-right">{{ collection.mint_at }}</div>
            </BoxRow>
        </Box>
        
    </AuthenticatedLayout>
</template>