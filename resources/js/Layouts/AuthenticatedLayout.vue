<script setup>
import Navigation from '@/Components/Navigation.vue'
import Button from '@/Components/Form/Button.vue'
import { inject, ref } from 'vue'
import { switchChainTo } from '@/Wallets/MetaMask'
import { connectWallet } from '@/Wallets/Wallet'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    chainId: Number,
    loading: Boolean,
    transaction: [Boolean, String],
    validBlockchain: [Boolean, String],
})
let transactionMessage = ref(false)
const emitter = inject('emitter')

emitter.on('set-transaction', (message) => {
    transactionMessage.value = message
})

const switchBlockchain = async () => {
    const status = await switchChainTo(props.chainId)
    if (status !== true) {
        emitter.emit('new-message', {type: 'error', message: status})
    }
}
</script>
<template>
    <div class="main-container min-h-screen bg-primary-100 dark:bg-mintpad-500">
        <Navigation />

        <div v-if="transaction != false || transactionMessage != false" class="fixed z-40 inset-0 bg-gray-200 dark:bg-mintpad-500 bg-opacity-50 dark:bg-opacity-50 transition-opacity">
            <Modal :show="true" :show-close="false" title="Transaction status">
                <div class="flex items-center">
                    <div class="pr-4">
                        <i class="text-3xl fa-solid fa-spinner animate-spin"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ transaction != false ? transaction : transactionMessage }}</h3>
                        <p>Your wallet will prompt you to sign the transaction</p>
                    </div>
                </div>
            </Modal>
        </div>
        <div v-if="loading" class="w-10 mx-auto pt-4 text-lg dark:text-white z-50"><img src="/images/icon.svg" class="h-[35px] animate-bounce" /></div>
        <div v-else>
            <div class="col-span-1 lg:col-span-2">
                <div v-if="validBlockchain == 'wallet'" class="bg-mintpad-200 dark:bg-mintpad-700 p-2 mb-4 text-center">
                    <p class="text-sm text-mintpad-700 dark:text-white">Your wallet is not connected <Button href="#" class="ml-4" @click.prevent="connectWallet('metamask')">Connect MetaMask</Button></p>
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