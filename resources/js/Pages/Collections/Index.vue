<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import LinkBlue from '@/Components/LinkBlue.vue'
import Box from '@/Components/Box.vue'
import BoxRow from '@/Components/BoxRow.vue'
import BoxContent from '@/Components/BoxContent.vue'
import Button from '@/Components/Form/Button.vue'
import ButtonGray from '@/Components/Form/ButtonGray.vue'
import { Head } from '@inertiajs/vue3'
import { ref, provide, onMounted } from 'vue'
import { connectWallet } from '@/Wallets/Wallet'
import { shortenWalletAddress, copyToClipboard } from '@/Helpers/Helpers'
import { getBlockchains } from '@/Helpers/Blockchain'
import Modal from '@/Components/Modal.vue'
import Hyperlink from '@/Components/Hyperlink.vue'

const props = defineProps({
    collections: Object
})
let validBlockchain = ref(true)
let wallet = ref(false)
let loading = ref(true)
let blockchains = ref(getBlockchains())
let showModal = ref(false)
provide('wallet', wallet)
provide('transaction', {show: false, message: ''})

onMounted(async () => {
    // Connect wallet if local storage is set
    const walletName = localStorage.getItem('walletName')
    if (walletName) {
        wallet.value = await connectWallet(walletName, false)
    }

    // Init app
    // validateBlockchain(collection.chain_id)

    // Done loading
    loading.value = false
})
</script>
<template>
    <AuthenticatedLayout :loading="loading" :valid-blockchain="validBlockchain">
        <Head title="Collections" />
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
            <Box class="bg-waves dark:bg-waves-dark bg-cover p-12">
                <h1>Welcome</h1>
                <p class="mb-4">Launching a collection can seem complicated. That is why we have made tutorials which explain the entire process step by step. 
                    You can find them in our detailed <Hyperlink element="a" class="text-sm" href="https://generator.mintpad.co" target="_blank">documentation</Hyperlink>.</p>
            </Box>
            <Box class="text-left md:text-center p-12">
                <h1>Let’s get started</h1>
                <div v-if="!wallet.account">
                    <p class="mb-4">You have to connect your wallet to start creating your collection.</p>
                    <Button href="#" @click.prevent="connectWallet('metamask', true)">Connect MetaMask</Button>
                </div>
                <div v-else>
                    <p class="mb-4">We are connected to your wallet.</p>
                    <LinkBlue :href="route('collections.create')">Create collection</LinkBlue>
                </div>
            </Box>
        </div>

        <Box class="w-full mb-12" title="Your collections">
            <div v-if="collections">
                <BoxRow class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                    <div class="basis-full sm:basis-3/12">Collection name</div>
                    <div class="hidden sm:block basis-2/12">Type</div>
                    <div class="hidden sm:block basis-3/12">Blockchain</div>
                    <div class="hidden sm:block basis-4/12 lg:basis-2/12">Contract address</div>
                    <div class="hidden sm:block basis-2/12"></div>
                </BoxRow>

                <BoxRow v-for="collection in collections" class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-full sm:basis-3/12">{{ collection.name }}</div>
                    <div class="hidden sm:block basis-2/12">{{ collection.type }}</div>
                    <div class="basis-full sm:basis-3/12">{{ blockchains[collection.chain_id].name }}</div>
                    <div class="basis-full sm:basis-4/12 lg:basis-2/12">
                        <ButtonGray content="Copy contract address" @click="copyToClipboard" :text="collection.address" class="!w-full !text-sm !bg-mintpad-100 dark:!bg-mintpad-700 !px-3 !py-1" v-tippy><i class="fas fa-copy mr-2 text-mintpad-700 dark:text-white"></i>{{ shortenWalletAddress(collection.address) }}</ButtonGray>
                    </div>
                    <div class="basis-full sm:basis-2/12 text-center sm:text-right">
                        <LinkBlue :href="route('collections.edit', collection.id)" class="!py-2">Manage</LinkBlue>
                    </div>
                </BoxRow>
            </div>
            <BoxContent v-else>
                <p>You don't have any collections yet</p>
            </BoxContent>
        </Box>

        <div class="text-center mt-10">
            <LinkBlue :href="route('collections.create')">Create collection</LinkBlue>
        </div>
    </AuthenticatedLayout>
</template>