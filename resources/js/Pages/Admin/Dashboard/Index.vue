<script setup>
import Box from '@/Components/Box.vue'
import BoxRow from '@/Components/BoxRow.vue'
import { getBlockchains } from '@/Helpers/Blockchain'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { getDefaultWalletData } from '@/Wallets/Wallet'
import { Head } from '@inertiajs/vue3'
import { ref, provide, onMounted } from 'vue'
import axios from 'axios'
import Toggle from '@/Components/Form/Toggle.vue'
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
let showUSD = ref(false)
let showTestnets = ref(false)

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
                <template v-slot:action>
                    <Toggle value="1" v-model:checked="showTestnets">Show Testnets</Toggle>
                </template>
                <BoxRow class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                    <div class="basis-2/3">Blockchain</div>
                    <div class="basis-1/3">Collections</div>
                </BoxRow>
                <div v-for="(amount, id) in chains">
                    <BoxRow v-if="(blockchains[id].testnet == false) || (blockchains[id].testnet == true && showTestnets)" class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/3">{{ blockchains[id].name }}</div>
                    <div class="basis-1/3">{{ amount }}</div>
                    </BoxRow>
                </div>
            </Box>

            <Box title="Wallet balances">
                <template v-slot:action>
                    <Toggle value="1" v-model:checked="showUSD">Show USD</Toggle>
                </template>
                <BoxRow class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                    <div class="basis-1/2">Blockchain</div>
                    <div class="basis-1/2">Balance</div>
                </BoxRow>
                <BoxRow v-for="(wallet, id) in wallets" class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-1/2">{{ blockchains[id].name }}</div>
                    <div class="basis-1/2">
                        <div v-if="walletBalances.data && walletBalances.data[id]">
                            <div v-if="showUSD">
                                <span class="font-mono">${{ walletBalances.data[id].usd }}</span>
                            </div>
                            <div v-else>
                                <span class="font-mono">{{ walletBalances.data[id].balance }} {{ blockchains[id].nativeCurrency.symbol }}</span>
                            </div>
                        </div>
                        <i v-else class="inline-block fa-solid fa-spinner animate-spin text-base mr-1 align-middle"></i>
                    </div>
                </BoxRow>
            </Box>

            <Box title="Stats">
                <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/3">Users</div>
                    <div class="basis-1/3">{{ users }}</div>
                </BoxRow>
                <BoxRow class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/3">Collections (Mainnet and Testnet)</div>
                    <div class="basis-1/3">{{ collections }}</div>
                </BoxRow>
            </Box>
        </div>
    </AuthenticatedLayout>
</template>