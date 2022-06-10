<x-app-layout>
    <div class="border border-gray-200 bg-white overflow-hidden sm:rounded-md">
        <div class="p-6 bg-white">
        <div class="text-center mb-10">
            <h2 class="font-bold text-3xl text-center mb-1">{{ __('Dashboard') }}</h2>
            <p v-if="message.error" class="border-l-2 border-red-500 text-red-500 pl-3 text-left mb-2">@{{ message.error }}</p>

            <div v-if="!account">
                <p class="text-center mb-4">We are not connected to your MetaMask account</p>
                <p class="text-center"><x-link href="#" @click.prevent="connectMetaMask">Connect MetaMask</x-link></p>
            </div>

            <div v-else>
                <p class="mb-4"><x-link href="" @click.prevent="deployContract">Deploy Smart Contract</x-link> as user with address <b>@{{ account }}</b></p>
                <p><x-input type="text" v-model="form.baseURI" /> <x-link href="" @click.prevent="setBaseURI">Update Base URI</x-link> OR <x-link href="" @click.prevent="getBaseURI">Get Base URI</x-link></p>
            </div>
        </div>
    </div>
</x-app-layout>
