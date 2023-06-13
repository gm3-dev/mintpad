<script setup>
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import Hyperlink from '@/Components/Hyperlink.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, provide, onMounted } from 'vue'
import _ from 'lodash'
import { format } from 'date-fns'
import { reconnectWallet } from '@/Wallets/Wallet'
import BoxRow from '@/Components/BoxRow.vue'
import LinkDarkBlue from '@/Components/LinkDarkBlue.vue'

const props = defineProps({
    invoices: Object
})

let loading = ref(true)
let wallet = ref(false)
let validBlockchain = ref(true)

provide('wallet', wallet)

onMounted(async () => {
    // Connect wallet
    wallet.value = await reconnectWallet()

    // Done loading
    loading.value = false
})
</script>
<template>
    <AuthenticatedLayout :loading="loading" :valid-blockchain="validBlockchain">
        <Head title="Invoices" />
        
        <div class="text-center mb-10">
            <h1>Invoices</h1>
            <p>Download your invoices here.</p>
        </div>

        <Box title="Your invoices">
            <BoxContent>
                <p>Are you a company? Add your details to your account <Hyperlink :href="route('users.profile')">here</Hyperlink>.</p>
            </BoxContent>
        </Box>

        <form @submit.prevent="route('users.update')">

            <Box title="Downloads">
                <BoxContent v-if="invoices.length == 0">
                    <p>You don't have any invoices yet.</p>
                </BoxContent>
                <div v-else>
                    <BoxRow class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                        <div class="basis-1/5">Month</div>
                        <div class="basis-3/5">Collection name</div>
                        <div class="basis-1/5"></div>
                    </BoxRow>
                    <BoxRow v-for="invoice in invoices" class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                        <div class="basis-1/5">{{ format(new Date(invoice.created_at), 'MMMM Y') }}</div>
                        <div class="basis-3/5">{{ invoice.reference }}</div>
                        <div class="basis-1/5 text-right">
                            <LinkDarkBlue element="a" :href="route('users.download', invoice.id)" target="_blank">Download</LinkDarkBlue>
                        </div>
                    </BoxRow>
                </div>
            </Box>
        </form>
    </AuthenticatedLayout>
</template>