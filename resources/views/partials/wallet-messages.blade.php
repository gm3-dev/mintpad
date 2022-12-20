<div v-if="hasValidChain !== true" class="border border-primary-600 rounded-lg p-4 py-8 mb-8">
    <div v-if="hasValidChain == 'chain' && collection.chain == 'evm'">
        <p class="text-sm text-center mb-4">Your wallet is not connected to the correct blockchain. Switching blockchains will refresh this page and all form data will be lost.</p>
        <p class="text-center"><x-link-button href="#" @click.prevent="switchBlockchainTo(false)">Switch blockchain</x-link-button></p>
    </div>
    <div v-if="hasValidChain == 'wallet'">
        <p class="text-sm text-center">You are connected to the wrong wallet, connect to the correct wallet manually.</p>
    </div>
</div>