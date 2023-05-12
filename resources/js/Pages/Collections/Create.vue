<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Button from '@/Components/Form/Button.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, provide, onMounted, watch, computed } from 'vue'
import { connectWallet } from '@/Wallets/Wallet'
import BoxContent from '@/Components/BoxContent.vue'
import Box from '@/Components/Box.vue'
import Label from '@/Components/Form/Label.vue'
import Input from '@/Components/Form/Input.vue'
import Textarea from '@/Components/Form/Textarea.vue'
import Select from '@/Components/Form/Select.vue'
import Addon from '@/Components/Form/Addon.vue'
import { getBlockchains, checkCurrentBlockchain } from '@/Helpers/Blockchain'
import { getSelectInputBlockchainObject } from '@/Helpers/Helpers'
import { getSDKFromSigner } from '@/Helpers/Thirdweb'
import Messages from '@/Components/Messages.vue'
import { resportError } from '@/Helpers/Sentry'
import { getMetaMaskError } from '@/Wallets/MetaMask'
import axios from 'axios'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}

let wallet = ref(false)
let loading = ref(true)
let buttonLoading = ref(false)
let blockchains = ref(getBlockchains())
let blockchainList = ref({})
let validBlockchain = ref(false)
let messages = ref([])
let transaction = ref({show: false, message: ''})

const form = useForm({
    chain_id: 1,
    type: '',
    address: '',
    name: '',
    symbol: '',
    feeRecipient: '',
    royalties: 0,
    salesRecipient: ''
})
console.log(form)
provide('wallet', wallet)
provide('transaction', transaction)

onMounted(async () => {
    // Connect wallet if local storage is set
    const walletName = localStorage.getItem('walletName')
    if (walletName) {
        wallet.value = await connectWallet(walletName, false)
    }

    // Init app
    form.chain_id = wallet.value.chainId
    validBlockchain.value = checkCurrentBlockchain(blockchains, form.chain_id, wallet)
    blockchainList.value = getSelectInputBlockchainObject(blockchains)

    // Set form data
    form.salesRecipient = wallet.value.account
    form.feeRecipient = wallet.value.account

    // Done loading
    loading.value = false
})

watch(() => form.chain_id, (newChainId) => {
    validBlockchain.value = checkCurrentBlockchain(blockchains, parseInt(newChainId), wallet)
})

const selectContractType = (type) => {
    form.type = type
}

const deployContract = async () => {
    if (validBlockchain.value !== true) {
        messages.value.push({type: 'error', message: 'Please connect to the correct blockchain'})
        return
    }

    const currentBlockchain = blockchains.value[form.chain_id]
    let transactionFee = (0.001 * 1000000000000000000).toString()
    if (currentBlockchain.testnet == false) {
        const coingeckoData = await axios.get('https://api.coingecko.com/api/v3/simple/price?ids='+currentBlockchain.coingecko+'&vs_currencies=usd').then((response) => {
            return response
        })
        if (coingeckoData.data[currentBlockchain.coingecko] !== undefined) {
            const tokenPrice = coingeckoData.data[currentBlockchain.coingecko].usd
            transactionFee = ((1 / tokenPrice) * 1000000000000000000).toString()
        } else {
            messages.value.push({type: 'error', message: 'Error while setting contract data'})
            return
        }
    }

    // Validate form
    let error = false
    if (form.royalties.length < 1) {
        error = 'Creator royalties must be a number'
    } else if (form.royalties < 0 || form.royalties > 100) {
        error = 'Creator royalties must be a number between 0 and 100'
    } else if (form.symbol.length < 2) {
        error = 'Symbol / ticker must be at least 2 characters long'
    } else if (form.name.length < 3) {
        error = 'Collection name must be at least 3 characters long'
    } else if (form.salesRecipient.length < 10) {
        error = 'Recipient address is not valid'
    } else if (form.feeRecipient.length < 10) {
        error = 'Recipient address is not valid'
    }
    if (error) {
        messages.value.push({type: 'error', message: error})
        return
    }

    buttonLoading.value = 'Deploying contract'
    try {
        // Deploy contract
        const sdk = getSDKFromSigner(wallet.value.signer, form.chain_id)

        let parameters = [
            wallet.value.account, // _defaultAdmin
            form.name, // _name
            form.symbol, // _symbol
            form.salesRecipient, // _saleRecipient
            transactionFee, // _transactionFee
            form.feeRecipient, // _royaltyRecipient
            form.royalties * 100, // _royaltyBps
        ]
        
        let contractAddress = false
        try {
            if (form.type == 'ERC721') {
                contractAddress = await sdk.deployer.deployReleasedContract('0x892a99573583c6490526739bA38BaeFae10a84D4', 'MintpadERC721Drop', parameters)
            } else if (form.type == 'ERC1155') {
                contractAddress = await sdk.deployer.deployReleasedContract('0x892a99573583c6490526739bA38BaeFae10a84D4', 'MintpadERC1155Drop', parameters)
            } else if (form.type == 'ERC1155Evolve') {
                contractAddress = await sdk.deployer.deployReleasedContract('0x892a99573583c6490526739bA38BaeFae10a84D4', 'MintpadERC1155Evolve', parameters)
            } else {
                throw new Error('Invalid contract type: ' + form.type)
            }
        } catch (error) {
            let metamaskError = getMetaMaskError(error)
            if (metamaskError) {
                messages.value.push({type: 'error', message: metamaskError})
            } else {
                resportError(error)
                messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
            }
        }

        if (contractAddress) {
            // Update DB
            form.address = contractAddress
            form.post(route('collections.store'), {})
        }
    } catch(error) {
        resportError(error)
        messages.value.push({type: 'error', message: 'Something went wrong, please try again.'})
    }

    buttonLoading.value = false
}
</script>
<template>
    <AuthenticatedLayout :loading="loading" :transaction="buttonLoading" :valid-blockchain="validBlockchain" :chain-id="parseInt(form.chain_id)">
        <Head title="Create collection" />

        <div v-if="!wallet.account"></div>
        <div v-else>
            <form @submit.prevent="submit" enctype="multipart/form-data">
                <div class="text-center mb-10">
                    <h1>Create NFT collection</h1>
                    <p>This is the start of your NFT collection.</p>
                </div>

                <Box v-if="form.type == ''" class="w-full mb-4" title="Choose your smart contract type">
                    <BoxContent class="flex gap-2 py-14">
                        <button @click.prevent="selectContractType('ERC721')" class="inline-block p-4 w-1/3 rounded-md bg-mintpad-200 dark:bg-mintpad-700 text-mintpad-700 dark:text-mintpad-200 mx-2 hover:text-mintpad-600 border border-transparent dark:hover:border-mintpad-400 transition ease-in-out duration-150">
                            <h2>NFT Drop</h2>
                            <p>Release collection of unique NFTs for a set price</p>
                        </button>
                        <button @click.prevent="selectContractType('ERC1155')" class="inline-block p-4 w-1/3 rounded-md bg-mintpad-200 dark:bg-mintpad-700 text-mintpad-700 dark:text-mintpad-200 mx-2 hover:text-mintpad-600 border border-transparent dark:hover:border-mintpad-400 transition ease-in-out duration-150">
                            <h2>Open Edition Drop</h2>
                            <p>Release ERC1155 tokens for a set price.</p>
                        </button>
                        <button @click.prevent="selectContractType('ERC1155Evolve')" class="inline-block p-4 w-1/3 rounded-md bg-mintpad-200 dark:bg-mintpad-700 text-mintpad-700 dark:text-mintpad-200 mx-2 hover:text-mintpad-600 border border-transparent dark:hover:border-mintpad-400 transition ease-in-out duration-150">
                            <h2>Open Edition Evolution Drop</h2>
                            <p>Release ERC1155 tokens for a set price with the possibility to evolve them by burning NFTs.</p>
                        </button>
                    </BoxContent>
                </Box>

                <Box v-else class="w-full mb-4" :title="form.type+' smart contract settings'">
                    <BoxContent>
                        <div class="w-full flex flex-wrap">
                            <div class="basis-full sm:basis-1/2">
                                <Label for="symbol" value="Blockchain" class="relative" info="Choose which blockchain you want to launch your NFT collection on." />
                                <Select class="!w-full mb-4" v-model="form.chain_id" :options="blockchainList"></Select>
                            </div>
                            <div class="basis-full sm:basis-1/2 px-0 sm:pl-4">
                                <Label for="symbol" value="Symbol / Ticker" class="relative" info="You can compare the symbol with a stock ticker. We recommend making this a shortened version of your collection's name. For example, for the collection name 'Mintpad NFT', the Symbol/Ticker could be 'MPNFT'. Keep it under 5 characters." />
                                <Input id="symbol" class="mb-4" type="text" v-model="form.symbol" />
                            </div>
                            <div class="basis-full">
                                <Label for="name" value="Collection name" class="relative" info="This is the name of your NFT collection." />
                                <Input id="name" class="mb-4" type="text" v-model="form.name" />
                            </div>
                            <div class="basis-full">
                                <Label for="sales_recipient" value="Sales recipient address" class="relative" info="This is the wallet address where the revenue from initial sales of your NFT collection go." />
                                <Input id="sales_recipient" class="w-full" v-model="form.salesRecipient" />
                            </div>
                            <div class="basis-full sm:basis-2/3">
                                <Label for="fee_recipient" value="Royalty recipient address" class="relative" info="This is the wallet address where the proceeds of your NFT collection go. By default, this is the wallet address that puts the NFT collection on the blockchain. Double check this address." />
                                <Input id="fee_recipient" class="w-full" v-model="form.feeRecipient" />
                            </div>
                            <div class="basis-full sm:basis-1/3 sm:pl-4">
                                <Label for="royalties" value="Creator royalties (%)" class="relative" info="This is how much percent you want to receive from secondary sales on marketplaces such as OpenSea and Magic Eden." />
                                <Addon position="right" content="%">
                                    <Input id="royalties" class="mb-4 addon-right" step=".01" min="0" max="100" type="number" v-model="form.royalties" />
                                </Addon>
                            </div>
                        </div>
                    </BoxContent>
                </Box>

                <div v-if="form.type" class="w-full">
                    <span class="inline-block" content="This action will trigger a transaction" v-tippy>
                        <Button href="#" @click.prevent="deployContract" :disabled="validBlockchain !== true" :loading="buttonLoading">Deploy smart contract</Button>
                    </span>
                </div>
            </form>
        </div>
        
        <Messages :messages="messages"/>
    </AuthenticatedLayout>
</template>