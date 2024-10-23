<script setup>
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import Addon from '@/Components/Form/Addon.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import Label from '@/Components/Form/Label.vue'
import Select from '@/Components/Form/Select.vue'
import ValidationMessage from '@/Components/Form/ValidationMessage.vue'
import { getBlockchains } from '@/Helpers/Blockchain'
import { getSelectInputBlockchainObject } from '@/Helpers/Helpers'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { getDefaultWalletData, reconnectWallet } from '@/Wallets/Wallet'
import { Head, useForm } from '@inertiajs/vue3'
import { onMounted, provide, ref } from 'vue'

const props = defineProps({
    'users': Object
})

let loading = ref(true)
let wallet = ref(getDefaultWalletData())
let blockchains = ref(getBlockchains())
let blockchainList = ref({})
let collection = useForm({
    'user_id': 1,
    'type': 'ERC721',
    'name': '',
    'description': '',
    'symbol': '',
    'royalties': 0,
    'chain_id': 1,
    'address': '',
    'permalink': '',
})

provide('wallet', wallet)

onMounted(async () => {
    // Connect wallet
    wallet.value = await reconnectWallet()

    blockchainList.value = getSelectInputBlockchainObject(blockchains)
    
    // Done loading
    loading.value = false
})

const submit = () => {
    collection.post(route('admin.collections.store'))
}
</script>
<template>
    <AuthenticatedLayout :loading="loading" :valid-blockchain="true">
        <Head title="New collection" />

        <div class="text-center mb-10">
            <h1>New collection</h1>
            <p>This form does not deploy any contract to the blockchain. This page can be used to manually add a collection in case of need.</p>
        </div>

        <form @submit.prevent="submit">
            <Box title="Collection data">
                <BoxContent>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                        <!-- User -->
                        <div class="relative">
                            <Label for="user" value="User" />
                            <Select id="user" class="block !w-full" v-model="collection.user_id" :options="users"></Select>
                        </div>
                        <!-- Type -->
                        <div class="relative">
                            <Label for="type" value="Type" />
                            <Select id="type" class="block !w-full" v-model="collection.type" :options="{'ERC721': 'ERC721', 'ERC1155': 'ERC1155', 'ERC1155Burn': 'ERC1155Burn'}"></Select>
                        </div>
                        <!-- Blockchain -->
                        <div class="relative">
                            <Label for="chain_id" value="Blockchain" />
                            <Select id="chain_id" class="block !w-full" v-model="collection.chain_id" :options="blockchainList"></Select>
                        </div>
                        <!-- Name -->
                        <div class="relative">
                            <Label for="name" value="Name" />
                            <Input type="text" id="name" class="block !w-full" v-model="collection.name" />
                            <ValidationMessage :validation="collection.errors.name" />
                        </div>
                        <!-- Description -->
                        <div class="relative">
                            <Label for="description" value="Description" />
                            <Input type="text" id="description" class="block !w-full" v-model="collection.description" />
                            <ValidationMessage :validation="collection.errors.description" />
                        </div>
                        <!-- Symbol -->
                        <div class="relative">
                            <Label for="symbol" value="Symbol" />
                            <Input type="text" id="symbol" class="block !w-full" v-model="collection.symbol" />
                            <ValidationMessage :validation="collection.errors.symbol" />
                        </div>
                        <!-- Address -->
                        <div class="relative">
                            <Label for="address" value="Address" />
                            <Input type="text" id="address" class="block !w-full" v-model="collection.address" />
                            <ValidationMessage :validation="collection.errors.address" />
                        </div>
                        <!-- Permalink -->
                        <div class="relative">
                            <Label for="permalink" value="Permalink" />
                            <Input type="text" id="permalink" class="block !w-full" v-model="collection.permalink" />
                            <ValidationMessage :validation="collection.errors.permalink" />
                        </div>
                        <!-- Royalties -->
                        <div class="relative">
                            <Label for="royalties" value="Royalties" />
                            <Addon position="right" content="%">
                                <Input id="royalties" class="mb-4 addon-right" step=".01" min="0" max="100" type="number" v-model="collection.royalties" />
                            </Addon>
                            <ValidationMessage :validation="collection.errors.royalties" />
                        </div>
                    </div>
                </BoxContent>
            </Box>

            <div>
                <Button>Create</Button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>