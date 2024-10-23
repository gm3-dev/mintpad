<script setup>
import ButtonGray from '@/Components/Form/ButtonGray.vue';

import { inject } from 'vue'
import { shortenWalletAddress } from '@/Helpers/Helpers'
import { copyToClipboard } from '@/Helpers/Helpers'

const wallet = inject('wallet')
</script>
<template>
    <div v-if="wallet.account" class="lg:inline-block">
        <ButtonGray href="#" @click.prevent="copyToClipboard" :text="wallet.account" class="w-40 !text-sm !px-3 !py-1 mr-4" content="Copy wallet address" v-tippy><i class="fas fa-copy mr-2 text-mintpad-700 dark:text-white"></i>{{ shortenWalletAddress(wallet.account) }}</ButtonGray>
    </div>      
</template>