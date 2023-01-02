<div v-if="hasValidChain !== true" class="bg-white border border-primary-600 rounded-md p-4 py-6 mb-8">
    <div v-if="hasValidChain == 'wallet'">
        <p class="text-sm text-center">You are connected to the wrong wallet, connect to the correct wallet manually.</p>
    </div>
</div>