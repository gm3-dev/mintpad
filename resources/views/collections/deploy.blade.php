<x-app-layout>
    <x-slot:header>
        <x-back-button href="{{ route('collections.index') }}">{{ __('Back') }}</x-back-button>
    </x-slot:header>
    
    <div class="bg-white overflow-hidden">
        <div class="text-center mb-10">
            <h2 class="font-bold text-3xl text-center mb-1">{{ __('Deploy your NFT collection') }}</h2>
            <p class="text-gray-500 text-center mb-5">{{ __('Make it official!') }}</p>
        </div>

        @if (isset($collection))
            <form method="POST" action="{{ route('collections.update', $collection->id) }}" enctype="multipart/form-data">
            @method('PUT')
        @else
            <form method="POST" action="{{ route('collections.store') }}" enctype="multipart/form-data">
        @endif
            @csrf

            <p v-if="message.error" class="px-6 py-4 rounded-md border border-red-500 mb-4 text-center">@{{ message.error }}</p>

            @if (isset($collection) && $collection->deployed)
                <div class="px-6 py-4 rounded-md border border-gray-200">
                    <p class="text-center">This smart contract has been deployed to the blockchain and cannot be changed.</p>
                </div>
            @endif

            @if (isset($collection) && !$collection->deployed)
                <div v-if="!account" class="p-6 text-center w-full sm:max-w-3xl mx-auto">
                    <h3 class="text-xl font-bold mb-4">Deploy smart contract</h3>
                    <p class="text-center mb-4">We are not connected to your MetaMask account</p>
                    <p class="text-center"><x-link href="#" @click.prevent="connectMetaMask">Connect MetaMask</x-link></p>
                </div>
                <div v-else class="p-6 text-center w-full sm:max-w-3xl mx-auto">
                    <p class="text-sm text-left">1) Smart contracts are immutable which means that once you create there is no way to alter them.</p>
                    <p class="text-sm text-left">2) Deploying a smart contract costs ETH which is a transaction fee for using the network.</p>
                    <p class="text-sm text-left mb-4">3) The smart contracts owner will be your current wallet account. Mintpad is NOT the smart contract owner.</p>

                    <x-input id="accept-conditions" v-model="agreeConditions" class="inline-block p-2 text-primary-600" type="checkbox" name="accept-conditions" value="1" />
                    <x-label for="accept-conditions" :value="__('I agree to the privacy policy and terms of conditions.')" class="inline-block" /><br/>
                    <x-button href="#" class="ml-4 mt-4" @click.prevent="deployContract" v-bind:disabled="!agreeConditions">{{ __('Deploy smart contract') }}</x-button>
                </div>
            @endif            
        </form>
    </div>
</x-app-layout>
