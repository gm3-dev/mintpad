<x-app-layout>
    <div class="border border-gray-200 bg-white overflow-hidden sm:rounded-md">
        <div class="p-6 bg-white">
        <div class="text-center mb-10">
            <h2 class="font-bold text-3xl text-center mb-1">{{ __('Dashboard') }}</h2>
            <p v-if="message.error" class="border-l-2 border-red-500 text-red-500 pl-3 text-left mb-2">@{{ message.error }}</p>

            <div v-if="!wallet.account">
                <p class="text-center mb-4">We are not connected to your MetaMask account</p>
                <p class="text-center"><x-link-button href="#" @click.prevent="connectMetaMask">Connect MetaMask</x-link-button></p>
            </div>

            <div v-else>
                <p class="mb-4">Connected with address <b>@{{ wallet.account }}</b></p>
            </div>
        </div>
    </div>
</x-app-layout>
