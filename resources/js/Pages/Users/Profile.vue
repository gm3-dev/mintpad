<script setup>
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import Label from '@/Components/Form/Label.vue'
import Hyperlink from '@/Components/Hyperlink.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, provide, onMounted } from 'vue'
import _ from 'lodash'
import { reconnectWallet } from '@/Wallets/Wallet'
import ShowPassword from '@/Components/ShowPassword.vue'
import ValidationMessage from '@/Components/Form/ValidationMessage.vue'

const props = defineProps({
    user: Object,
})

let loading = ref(true)
let wallet = ref(false)
let validBlockchain = ref(true)
let errorClasses = ref('!border-red-500')

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    new_password: null,
    new_password_confirmation: null,
    affiliate_code: props.user.affiliate_code,
})

provide('wallet', wallet)
provide('transaction', {show: false, message: ''})

onMounted(async () => {
    // Connect wallet
    wallet.value = await reconnectWallet()

    // Done loading
    loading.value = false
})

const submit = () => {
    form.put(route('user-profile-information.update'))
}
</script>
<template>
    <AuthenticatedLayout :loading="loading" :valid-blockchain="validBlockchain">
        <Head title="Your profile" />
        
        <div class="text-center mb-10">
            <h1>Your profile</h1>
            <p>Update your personal information here.</p>
        </div>

        <form @submit.prevent="submit">

            <Box title="Personal information">
                <BoxContent>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                        <!-- Name -->
                        <div>
                            <Label for="name" value="Name" class="relative is-required" />
                            <Input id="name" type="text" v-model="form.name" required autofocus />
                        </div>
                        <!-- Email Address -->
                        <div>
                            <Label for="email" value="Email" />
                            <span class="inline-block w-full" content="You can't change your email address" v-tippy>
                                <Input id="email" type="email" v-model="form.email" disabled />
                            </span>
                        </div>
                    </div>
                </BoxContent>
            </Box>

            <Box title="Password reset">
                <BoxContent>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                        <!-- Password -->
                        <div class="relative">
                            <Label for="new-password" value="New password" />
                            <ShowPassword v-slot="slotProps">
                                <Input id="password" :type="slotProps.type" v-model="form.new_password" :class="[form.errors.new_password ? errorClasses : '']" autocomplete="new-password"/>
                            </ShowPassword>
                            <ValidationMessage :validation="form.errors.new_password" />
                        </div>
                        <!-- Confirm Password -->
                        <div class="relative">
                            <Label for="confirm-new-password" value="Confirm new password" />
                            <ShowPassword v-slot="slotProps">
                                <Input id="confirm-new-password" class="mb-0" :type="slotProps.type" v-model="form.new_password_confirmation" autocomplete="confirm-new-password"/>
                            </ShowPassword>
                        </div>
                    </div>
                </BoxContent>
            </Box>

            <Box v-if="user.role == 'affiliate'" title="Affiliate information">
                <BoxContent>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                        <!-- Affiliate code -->
                        <div>
                            <Label for="affiliate_code" value="Affiliate code" />
                            <Input id="affiliate_code" type="text" v-model="form.affiliate_code" disabled />
                        </div>
                        <!-- Affiliate code -->
                        <div>
                            <Label for="affiliate_url" value="Your register URL" />
                            <p class="mt-2"><Hyperlink target="_blank" :href="route('register')+'/?affiliate='+user.affiliate_code">{{ route('register') }}/?affiliate={{ user.affiliate_code }}</Hyperlink></p>
                        </div>
                    </div>
                </BoxContent>
            </Box>

            <div class="w-full">
                <Button>Update profile</Button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>