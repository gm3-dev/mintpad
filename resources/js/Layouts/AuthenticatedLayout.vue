<script setup>
import Navigation from '@/Components/Navigation.vue'
import Button from '@/Components/Form/Button.vue'
import { inject } from 'vue'
import { switchBlockchainTo } from '@/Wallets/MetaMask'
import { connectWallet } from '@/Wallets/Wallet'

const props = defineProps({
    chainId: Number,
    loading: Boolean,
    overlay: Boolean,
    validBlockchain: [Boolean, String]
})
const emitter = inject('emitter')

const switchBlockchain = async () => {
    const status = await switchBlockchainTo(props.chainId)
    if (status !== true) {
        emitter.emit('new-message', {type: 'error', message: status})
    }
}
</script>
<template>
    <div class="main-container min-h-screen bg-primary-100 dark:bg-mintpad-500">
        <Navigation />

        <div v-if="overlay" class="fixed z-40 inset-0 bg-gray-200 dark:bg-mintpad-500 bg-opacity-50 dark:bg-opacity-50 transition-opacity">
            <i class="absolute top-1/2 left-1/2 -ml-[12px] -mt-[12px] text-3xl fa-solid fa-spinner animate-spin"></i>
        </div>
        <div v-if="loading" class="w-10 mx-auto pt-4 text-lg dark:text-white z-50"><img src="/images/icon.svg" class="h-[35px] animate-bounce" /></div>
        <div v-else>
            <div class="col-span-1 lg:col-span-2">
                <div v-if="validBlockchain == 'wallet'" class="bg-mintpad-200 dark:bg-mintpad-700 p-2 mb-4 text-center">
                    <p class="text-sm text-mintpad-700 dark:text-white">Your wallet is not connected <Button href="#" class="ml-4" @click.prevent="connectWallet('metamask', true)">Connect MetaMask</Button></p>
                </div>
                <div v-else-if="validBlockchain == 'chain'" class="bg-mintpad-200 dark:bg-mintpad-700 p-2 mb-4 text-center">
                    <p class="text-sm text-mintpad-700 dark:text-white">Your wallet is not connected to the correct blockchain <Button href="#" class="ml-4" @click.prevent="switchBlockchain">Switch blockchain</Button></p>
                </div>
            </div>
            <div class="py-12 max-w-7xl mx-auto px-6 relative">
                <slot />

            </div>
        </div>
    </div>
</template>