<script setup>
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Button from '@/Components/Form/Button.vue'
import Label from '@/Components/Form/Label.vue'
import Select from '@/Components/Form/Select.vue'
import Hyperlink from '@/Components/Hyperlink.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, provide, onMounted } from 'vue'
import { getDefaultWalletData } from '@/Wallets/Wallet'

const props = defineProps({
    'user': Object,
    'roles': Object,
})

const form = useForm({
    role: props.user.role,
    status: props.user.status
})

let loading = ref(true)
let wallet = ref(getDefaultWalletData())
let validBlockchain = ref(true)

provide('wallet', wallet)
provide('transaction', {show: false, message: ''})

onMounted(async () => {
    // Done loading
    loading.value = false
})

const submit = () => {
    form.put(route('admin.users.update', props.user.id))
}
</script>
<template>
    <AuthenticatedLayout :loading="loading" :valid-blockchain="validBlockchain">
        <Head title="Edit user" />

        <div class="text-center mb-10">
            <h1>Edit user: {{ user.name }}</h1>
        </div>
        
        <form @submit.prevent="submit">
            <Box title="Settings">
                <BoxContent>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                        <!-- Role -->
                        <div>
                            <Label for="role" value="Role" />
                            <Select id="role" class="block !w-full" v-model="form.role" :options="roles"></Select>
                        </div>
                        <!-- Status -->
                        <div>
                            <Label for="status" value="Status" />
                            <Select id="status" class="block !w-full" v-model="form.status" :options="{'active': 'Active', 'blocked': 'Blocked'}"></Select>
                        </div>
                    </div>
                </BoxContent>
            </Box>

            <Box title="Personal information">
                <BoxContent>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                        <!-- Name -->
                        <div>
                            <Label for="name" value="Name" />
                            <p>{{ user.name }}</p>
                        </div>
                        <!-- Email Address -->
                        <div>
                            <Label for="email" value="Email" />
                            <p>{{ user.email }}</p>
                        </div>
                    </div>
                </BoxContent>
            </Box>

            <Box title="Affiliate information">
                <BoxContent>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                        <div v-if="user.role == 'affiliate'">
                            <!-- Affiliate code -->
                            <div>
                                <Label for="affiliate_code" value="Affiliate code" />
                                <p>{{ user.affiliate_code ?? '-' }}</p>
                            </div>
                            <!-- Affiliate code -->
                            <div>
                                <Label for="affiliate_url" value="Your register URL" />
                                <p v-if="user.affiliate_code"><Hyperlink element="a" target="_blank" :href="route('register')+'/?affiliate='+user.affiliate_code">{{ route('register') }}/?affiliate={{ user.affiliate_code }}</Hyperlink></p>
                                <p v-else>-</p>
                            </div>
                        </div>
                        <p v-else>-</p>
                    </div>
                </BoxContent>
            </Box>

            <div>
                <Button>Update</Button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>