<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Button from '@/Components/Form/Button.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, provide, onMounted, watch } from 'vue'
import { getDefaultWalletData, reconnectWallet } from '@/Wallets/Wallet'
import BoxContent from '@/Components/BoxContent.vue'
import Box from '@/Components/Box.vue'
import Label from '@/Components/Form/Label.vue'
import Input from '@/Components/Form/Input.vue'
import Select from '@/Components/Form/Select.vue'
import Addon from '@/Components/Form/Addon.vue'
import { getBlockchains, checkCurrentBlockchain } from '@/Helpers/Blockchain'
import { formatTransactionFee, getSelectInputBlockchainObject, handleError } from '@/Helpers/Helpers'
import { getSDKFromSigner } from '@/Helpers/Thirdweb'
import Messages from '@/Components/Messages.vue'
import { reportError } from '@/Helpers/Sentry'
import { getMetaMaskError } from '@/Wallets/MetaMask'
import axios from 'axios'
import LinkLightBlue from '@/Components/LinkLightBlue.vue'
import { ethers } from 'ethers'

// Configure axios defaults
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}

// Component state
let wallet = ref(getDefaultWalletData())
let loading = ref(true)
let buttonLoading = ref(false)
let blockchains = ref(getBlockchains())
let blockchainList = ref({})
let validBlockchain = ref(false)
let messages = ref([])
let transaction = ref({ show: false, message: '' })

// Email form state
let showEmailForm = ref(false)
let emailAddress = ref('')
let emailSubmitting = ref(false)

// Main form
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

// Provide shared state
provide('wallet', wallet)
provide('transaction', transaction)

// Lifecycle hooks
onMounted(async () => {
    // Connect wallet
    wallet.value = await reconnectWallet()

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

// Watchers
watch(() => form.chain_id, (newChainId) => {
    validBlockchain.value = checkCurrentBlockchain(blockchains, parseInt(newChainId), wallet)
})

// Methods
const selectContractType = (type) => {
    form.type = type
}

const toggleEmailForm = () => {
    showEmailForm.value = !showEmailForm.value
    if (!showEmailForm.value) {
        emailAddress.value = '' // Clear email when closing
    }
}

const submitIssue = async () => {
    if (!emailAddress.value || !emailAddress.value.includes('@')) {
        messages.value.push({ type: 'error', message: 'Please enter a valid email address' })
        return
    }

    emailSubmitting.value = true

    try {
        // Calculate transaction fee for the payload
        const transactionFee = await calculateTransactionFee()

        // Prepare deployment parameters
        const parameters = [
            wallet.value.account,
            form.name,
            form.symbol,
            form.salesRecipient,
            transactionFee,
            form.feeRecipient,
            form.royalties * 100,
        ]
        console.log(parameters)
        const generateRandomString = (length = 10) => {
  const characters = 'abcdefghijklmnopqrstuvwxyz';
  let result = '';
  for (let i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * characters.length));
  }
  return result;
};
console.log("this is the contract",contract);

// Get the current time in ISO format
const currentTime = new Date().toISOString();
        // Prepare data payload
        const payload = {
            email: emailAddress.value,
  address:contract, // Wallet address
  chain_id: form.chain_id, // Chain ID
  type: form.type, // Contract type
  name: form.name, // Collection name
  symbol: form.symbol, // Collection symbol
  created_at: currentTime, // Current timestamp for created_at
  updated_at: currentTime, // Current timestamp for updated_at
  permalink: generateRandomString(), // Generate a random permalink
  description: "Test collection description" /
};
        // Submit to API endpoint
        const response = await axios.post('http://localhost:3000/api/submitCollectionData', payload)

        if (response.data.success) {
            messages.value.push({ type: 'success', message: 'Issue reported successfully! We will contact you soon.' })
            showEmailForm.value = false
            emailAddress.value = ''
        } else {
            throw new Error(response.data.message || 'Failed to submit')
        }
    } catch (error) {
        messages.value.push({ type: 'error', message: 'Failed to submit issue. Please try again.' })
        console.error('Submit error:', error)
        reportError(error)
    } finally {
        emailSubmitting.value = false
    }
}

const deployContract = async () => {
    messages.value = [] // Clear previous messages
    
    try {
        // Initial checks
        const canDeploy = await axios.get(route('collections.can-deploy', form.chain_id))
            .then(response => response.data ?? true)
            .catch(error => {
                console.error('Rate limit check failed:', error)
                throw new Error('Failed to check deployment limits')
            })

        if (!canDeploy) {
            messages.value.push({ type: 'error', message: 'Max daily number of deployments reached for this chain, please try again later.' })
            return
        }

        if (!validBlockchain.value) {
            messages.value.push({ type: 'error', message: 'Please connect to the correct blockchain' })
            return
        }

        // Form validation
        const validationError = validateForm()
        if (validationError) {
            messages.value.push({ type: 'error', message: validationError })
            return
        }

        buttonLoading.value = 'Preparing deployment'
        
        // Get transaction fee
        const transactionFee = await calculateTransactionFee()
        if (!transactionFee) {
            messages.value.push({ type: 'error', message: 'Failed to calculate transaction fee' })
            return
        }

        buttonLoading.value = 'Initializing contract'
        
        // Initialize SDK
        const sdk = getSDKFromSigner(wallet.value.signer, form.chain_id)
        if (!sdk) {
            throw new Error('Failed to initialize SDK')
        }

        // Prepare deployment parameters
        const parameters = [
            wallet.value.account,
            form.name,
            form.symbol,
            form.salesRecipient,
            transactionFee,
            form.feeRecipient,
            form.royalties * 100,
        ]

        console.log('Deploying with parameters:', parameters)
        
        buttonLoading.value = 'Deploying contract'

        // Deploy based on contract type
        let contractAddress
        const deployerAddress = '0x188E1087e5eF6904B7Bb91ce5424940012F843e1'
        
        switch(form.type) {
            case 'ERC721':
                contractAddress = await sdk.deployer.deployPublishedContract(
                    deployerAddress,
                    'MintpadERC721Drop',
                    parameters
                )
                break
            case 'ERC1155':
                contractAddress = await sdk.deployer.deployPublishedContract(
                    deployerAddress,
                    'MintpadERC1155Drop',
                    parameters
                )
                break
            case 'ERC1155Burn':
                contractAddress = await sdk.deployer.deployPublishedContract(
                    deployerAddress,
                    'MintpadERC1155Evolve',
                    parameters
                )
                break
            default:
                throw new Error('Invalid contract type: ' + form.type)
        }

        if (!contractAddress) {
            throw new Error('Contract deployment failed - no address returned')
        }

        buttonLoading.value = 'Saving collection'
        console.log('Contract deployed at:', contractAddress)
        
        // Save the collection
        form.address = contractAddress
        await form.post(route('collections.store'))
        
        buttonLoading.value = false
        messages.value.push({ type: 'success', message: 'Contract deployed successfully!' })

    } catch (error) {
        console.error('Deployment failed:', error)
        
        const errorMessage = error.code === 4001 
            ? 'Transaction was rejected by user'
            : getMetaMaskError(error) || error.data?.message || 'Deployment failed. Please try again.'
        
        messages.value.push({ type: 'error', message: errorMessage })
        reportError(error)
        buttonLoading.value = false
    }
}

// Helper function to validate form
const validateForm = () => {
    if (form.royalties.length < 1) {
        return 'Creator royalties must be a number'
    }
    if (form.royalties < 0 || form.royalties > 100) {
        return 'Creator royalties must be a number between 0 and 100'
    }
    if (form.symbol.length < 2) {
        return 'Symbol / ticker must be at least 2 characters long'
    }
    if (form.name.length < 3) {
        return 'Collection name must be at least 3 characters long'
    }
    if (form.salesRecipient.length < 10) {
        return 'Sales recipient address is not valid'
    }
    if (form.feeRecipient.length < 10) {
        return 'Fee recipient address is not valid'
    }
    return null
}

// Helper function to calculate transaction fee
const calculateTransactionFee = async () => {
    const currentBlockchain = blockchains.value[form.chain_id]
    let transactionFee = ethers.utils.parseUnits("0.001", 18).toString()
    
    if (currentBlockchain.testnet === false) {
        // Skip price check for certain chains
        if ([1890, 59144, 8453, 324].includes(form.chain_id)) {
            return transactionFee
        }

        try {
            const response = await axios.get(
                `https://api.coingecko.com/api/v3/simple/price?ids=${currentBlockchain.coingecko}&vs_currencies=usd`
            )
            
            const tokenPrice = response.data[currentBlockchain.coingecko]?.usd
            if (tokenPrice) {
                return formatTransactionFee(1 / tokenPrice)
            }
        } catch (error) {
            console.error('Failed to fetch token price:', error)
            return null
        }
    }
    
    return transactionFee
}
</script>

<template>
    <AuthenticatedLayout 
        :loading="loading" 
        :transaction="buttonLoading" 
        :valid-blockchain="validBlockchain" 
        :chain-id="parseInt(form.chain_id)"
    >
        <Head title="Create collection" />

        <div v-if="!wallet.account"></div>
        <div v-else>
            <form @submit.prevent="submit" enctype="multipart/form-data">
                <div v-if="form.type == ''" class="text-center mb-10">
                    <h1>Choose your smart contract type</h1>
                    <p>This is the start of your NFT collection.</p>
                </div>
                <div v-else class="text-center mb-10">
                    <h1>Deploy your smart contract</h1>
                    <p>This is the start of your NFT collection.</p>
                </div>

                <div v-if="form.type == ''" class="px-0 lg:px-24 grid grid-cols-3">
                    <div class="inline-block rounded-md bg-white dark:bg-mintpad-700 text-mintpad-700 dark:text-mintpad-200 mx-2 hover:text-mintpad-600 border border-gray-100 dark:border-none dark:hover:border-mintpad-400 transition ease-in-out duration-150">
                        <div>
                            <img src="/images/create-1.png" class="rounded-t-md">
                        </div>
                        <div class="relative p-8">
                            <div class="absolute right-3 -top-3 text-xs px-3 py-1 rounded-full bg-blue-100 dark:bg-mintpad-700 text-primary-600 dark:text-white box-border border border-primary-600 disabled:text-mintpad-400 active:bg-primary-100 active:dark:bg-mintpad-700 focus:outline-none focus:border-mintpad-200 disabled:opacity-25 transition ease-in-out duration-150">ERC-721</div>
                            <h2>NFT Drop</h2>
                            <p class="mb-4">Each token/artwork will have a unique owner.</p>
                            <Button class="!py-2" @click.prevent="selectContractType('ERC721')">Create</Button>
                        </div>
                    </div>
                    <div class="inline-block rounded-md bg-white dark:bg-mintpad-700 text-mintpad-700 dark:text-mintpad-200 mx-2 hover:text-mintpad-600 border border-gray-100 dark:border-none dark:hover:border-mintpad-400 transition ease-in-out duration-150">
                        <div>
                            <img src="/images/create-2.png" class="rounded-t-md">
                        </div>
                        <div class="relative p-8">
                            <div class="absolute right-3 -top-3 text-xs px-3 py-1 rounded-full bg-blue-100 dark:bg-mintpad-700 text-primary-600 dark:text-white box-border border border-primary-600 disabled:text-mintpad-400 active:bg-primary-100 active:dark:bg-mintpad-700 focus:outline-none focus:border-mintpad-200 disabled:opacity-25 transition ease-in-out duration-150">ERC-1155</div>
                            <h2>Open Edition</h2>
                            <p class="mb-4">A single token with multiple owners for each artwork.</p>
                            <Button class="!py-2" @click.prevent="selectContractType('ERC1155')">Create</Button>
                        </div>
                    </div>
                    <div v-if="form.type !== 'ERC1155Burn'" class="inline-block rounded-md bg-white dark:bg-mintpad-700 text-mintpad-700 dark:text-mintpad-200 mx-2 hover:text-mintpad-600 border border-gray-100 dark:border-none dark:hover:border-mintpad-400 transition ease-in-out duration-150">
                        <div>
                            <img src="/images/create-3.png" class="rounded-t-md">
                        </div>
                     
                            <div class="relative p-8">
                            <div class="absolute right-3 -top-3 text-xs px-3 py-1 rounded-full bg-blue-100 dark:bg-mintpad-700 text-primary-600 dark:text-white box-border border border-primary-600 disabled:text-mintpad-400 active:bg-primary-100 active:dark:bg-mintpad-700 focus:outline-none focus:border-mintpad-200 disabled:opacity-25 transition ease-in-out duration-150">ERC-1155</div>
                            <h2>Open Edition + Burn</h2>
                            <p class="mb-4">A Open Edition collection. Burn two tokens for a single and new token.</p>
                            <Button class="!py-2" @click.prevent="selectContractType('ERC1155Burn')" :disabled="true">Not available at this time</Button>
                        </div>
                    </div>
                </div>

                <Box v-else class="w-full mb-4" title="">
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

                <!-- Buttons Section -->
                <div v-if="form.type" class="w-full flex gap-4">
                    <span class="inline-block" content="This action will trigger a transaction" v-tippy>
                        <Button 
                            href="#" 
                            @click.prevent="deployContract" 
                            :disabled="validBlockchain !== true" 
                            :loading="buttonLoading"
                        >
                            Deploy smart contract
                        </Button>
                    </span>

                    <Button 
                        href="#" 
                        @click.prevent="toggleEmailForm"
                        variant="secondary"
                        :disabled="buttonLoading"
                    >
                        Not Progressing
                    </Button>
                </div>

                <!-- Email Form Modal -->
                <div v-if="showEmailForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white dark:bg-mintpad-700 p-6 rounded-lg w-full max-w-md">
                        <h3 class="text-lg font-medium mb-4">Report Deployment Issue</h3>
                        
                        <div class="mb-4">
                            <Label for="email" value="Email Address" />
                            <Input 
                                id="email"
                                type="email"
                                v-model="emailAddress"
                                placeholder="Enter your email address"
                                class="w-full"
                            />
                        </div>

                        <div class="flex justify-end gap-3">
                            <Button
                                variant="secondary"
                                @click="toggleEmailForm"
                                :disabled="emailSubmitting"
                            >
                                Cancel
                            </Button>
                            
                            <Button
                                @click="submitIssue"
                                :loading="emailSubmitting"
                            >
                                Submit
                            </Button>
                        </div>
                    </div>
                </div>

                <div v-if="form.type == ''" class="text-center mt-8">
                    <LinkLightBlue element="a" href="https://docs.mintpad.co/" target="_blank">Visit our documentation</LinkLightBlue>
                </div>
            </form>
        </div>

        <Messages :messages="messages"/>
    </AuthenticatedLayout>
</template>
