<x-app-layout>
    <x-slot:header>
        <x-back-button href="{{ route('collections.index') }}">{{ __('Back') }}</x-back-button>
    </x-slot:header>

    <div class="bg-white overflow-hidden">
        <form method="POST" action="{{ route('collections.store') }}" enctype="multipart/form-data">
            <div class="text-center mb-10">
                <h2 class="font-bold text-3xl text-center mb-1">{{ __('Create NFT collection') }}</h2>
                <p class="text-gray-500 text-center mb-5">{{ __('Create your NFT collection.') }}</p>
            </div>

            <p v-if="message.error" class="px-6 py-4 rounded-md border border-red-500 mb-4 text-center">@{{ message.error }}</p>

            <div class="w-full flex flex-wrap">
                <div class="basis-full p-2">
                    <x-label for="name" :value="__('Name')" />
                    <x-input id="name" class="mt-1 w-full" type="text" name="name" v-model="forms.collection.name" required autofocus />
                </div>
                <div class="basis-1/3 p-2">
                    <x-label for="symbol" :value="__('Blockchain')" />
                    <x-select class="mt-1 !w-full" v-model="forms.collection.blockchain" :options="$blockchains"></x-select>
                </div>
                <div class="basis-1/3 p-2">
                    <x-label for="symbol" :value="__('Symbol')" />
                    <x-input id="symbol" class="mt-1 w-full" type="text" name="symbol" v-model="forms.collection.symbol" />
                </div>
                <div class="basis-1/3 p-2">
                    <x-label for="royalties" :value="__('Royalties (%)')" />
                    <x-input id="royalties" class="mt-1 w-full" step=".01" type="number" name="royalties" v-model="forms.collection.royalties" required />
                </div>
                <div class="basis-full p-2">
                    <x-label for="description" :value="__('Description')" />
                    <x-textarea id="description" class="mt-1 w-full" name="description" v-model="forms.collection.description"></x-textarea>
                </div>
            </div>

            <div class="text-center w-full p-6">
                <div class="p-6 text-center w-full sm:max-w-3xl mx-auto">
                    <p class="text-sm text-left">1) Smart contracts are immutable which means that once you create there is no way to alter them.</p>
                    <p class="text-sm text-left">2) Deploying a smart contract isn't free, you're paying a fee which is a transaction fee for using the network.</p>
                    <p class="text-sm text-left mb-4">3) The smart contracts owner will be your current wallet account. Mintpad is NOT the smart contract owner.</p>

                    <x-input id="accept-conditions" v-model="agreeConditions" class="inline-block !p-2 text-primary-600" type="checkbox" name="accept-conditions" value="1" />
                    <x-label for="accept-conditions" :value="__('I agree to the privacy policy and terms of conditions.')" class="inline-block" /><br/>
                    <x-button href="#" class="transaction-button ml-4 mt-4" @click.prevent="deployContract" v-bind:disabled="!agreeConditions"><i class="fas fa-exchange-alt mr-2"></i> {{ __('Deploy smart contract') }}</x-button>
                </div>
            </div>            
        </form>
    </div>
</x-app-layout>
