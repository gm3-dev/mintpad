<script setup>
import Box from '@/Components/Box.vue'
import BoxRow from '@/Components/BoxRow.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import LinkBlue from '@/Components/LinkBlue.vue'
import { Head } from '@inertiajs/vue3'
import { ref, provide, onMounted } from 'vue'
import { getDefaultWalletData } from '@/Wallets/Wallet'

defineProps({
})

let loading = ref(true)
let wallet = ref(getDefaultWalletData())
let validBlockchain = ref(true)

provide('wallet', wallet)

onMounted(async () => {
    // Done loading
    loading.value = false
})
</script>
<template>
    <AuthenticatedLayout :loading="loading" :valid-blockchain="validBlockchain">
        <Head title="Status" />

        <div class="text-center mb-10">
            <h1>Status</h1>
        </div>
        
        <Box title="Server status">
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">Platform live</div><div class="basis-3/5 text-green-700">Running with no known issues</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">Generator live</div><div class="basis-3/5 text-green-700">Running with no known issues</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">Platform development</div><div class="basis-3/5 text-green-700">Running with no known issues</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">Generator development</div><div class="basis-3/5 text-green-700">Running with no known issues</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">Demo website</div><div class="basis-3/5 text-green-700">Running with no known issues</div>
            </BoxRow>
        </Box>
        <Box title="Functionality status">
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">Register</div><div class="basis-3/5 text-green-700">No issues reported</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">My profile</div><div class="basis-3/5 text-green-700">No issues reported</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">Create collection</div><div class="basis-3/5 text-green-700">No issues reported</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">1. Settings</div><div class="basis-3/5 text-green-700">No issues reported</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">2. Mint phases</div><div class="basis-3/5 text-green-700">No issues reported</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">3. Upload collection</div><div class="basis-3/5 text-green-700">No issues reported</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">4. Mint settings</div><div class="basis-3/5 text-green-700">No issues reported</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">Collection page</div><div class="basis-3/5 text-green-700">No issues reported</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">Collection page editor</div><div class="basis-3/5 text-green-700">No issues reported</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">Invoices</div><div class="basis-3/5 text-green-700">No issues reported</div>
            </BoxRow>
            <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-2/5">Collection generator</div><div class="basis-3/5 text-green-700">No issues reported</div>
            </BoxRow>
        </Box>
        
    </AuthenticatedLayout>
</template>