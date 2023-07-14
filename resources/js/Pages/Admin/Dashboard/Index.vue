<script setup>
import Box from '@/Components/Box.vue'
import BoxRow from '@/Components/BoxRow.vue'
import { getBlockchains } from '@/Helpers/Blockchain'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { getDefaultWalletData } from '@/Wallets/Wallet'
import { Head } from '@inertiajs/vue3'
import { ref, provide, onMounted } from 'vue'
import axios from 'axios'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}

defineProps({
    'collections': Number,
    'chains': Object,
    'users': Number,
    'wallets': Object,
})

let loading = ref(true)
let wallet = ref(getDefaultWalletData())
let validBlockchain = ref(true)
let blockchains = ref(getBlockchains())
let walletBalances = ref([])

provide('wallet', wallet)
provide('transaction', {show: false, message: ''})

onMounted(async () => {
    // Done loading
    loading.value = false

    walletBalances.value = await axios.get('/admin/get-wallet-balances').then((response) => {
        return response
    })

    if (walletBalances.value.status !== 200) {
        walletBalances.value = []
    }
})
</script>
<template>
    <AuthenticatedLayout :loading="loading" :valid-blockchain="validBlockchain">
        <Head title="Dashboard" />

        <div class="text-center mb-10">
            <h1>Dashboard</h1>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
            <Box title="Collections">
                <BoxRow class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                    <div class="basis-2/3">Blockchain</div>
                    <div class="basis-1/3">Collections</div>
                </BoxRow>
                <BoxRow v-for="(amount, id) in chains" class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/3">{{ blockchains[id].name }}</div>
                    <div class="basis-1/3">{{ amount }}</div>
                </BoxRow>
                <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/3 pr-4 text-right">Total</div>
                    <div class="basis-1/3">{{ collections }}</div>
                </BoxRow>
            </Box>

            <Box title="Wallet balances">
                <BoxRow class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                    <div class="basis-1/2">Blockchain</div>
                    <div class="basis-1/2">Balance</div>
                </BoxRow>
                <BoxRow v-for="(wallet, id) in wallets" class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-1/2">{{ blockchains[id].name }}</div>
                    <div class="basis-1/2">
                        <span v-if="walletBalances.data && walletBalances.data[id]">{{ walletBalances.data[id] }} {{ blockchains[id].nativeCurrency.symbol }}</span>
                        <i v-else class="inline-block fa-solid fa-spinner animate-spin text-base mr-1 align-middle"></i>
                    </div>
                </BoxRow>
            </Box>

            <Box title="Users">
                <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/3">Total</div>
                    <div class="basis-1/3">{{ users }}</div>
                </BoxRow>
            </Box>
        </div>
    </AuthenticatedLayout>
</template>