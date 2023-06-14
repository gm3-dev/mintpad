<script setup>
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, provide, onMounted } from 'vue'
import { connectWallet } from '@/Wallets/Wallet'
import Label from '@/Components/Form/Label.vue'
import Input from '@/Components/Form/Input.vue'
import Button from '@/Components/Form/Button.vue'

const props = defineProps({
    user: Object
})

let loading = ref(true)
let wallet = ref(false)
let validBlockchain = ref(true)

const formToggle = useForm({})
const formConfirm = useForm({
    code: null
})

provide('wallet', wallet)

onMounted(async () => {
    // Connect wallet if local storage is set
    const walletName = localStorage.getItem('walletName')
    if (walletName) {
        wallet.value = await connectWallet(walletName, false)
    }

    // Done loading
    loading.value = false
})

const toggle = () => {
    if (props.user.two_factor_secret) {
        formToggle.delete(route('two-factor.disable'))
    } else {
        formToggle.post(route('two-factor.enable'))
    }
}
const confirm = () => {
    formConfirm.post(route('two-factor.confirm'))
}

</script>
<template>
    <AuthenticatedLayout :loading="loading" :valid-blockchain="validBlockchain">
        <Head title="Two-factor authentication" />
        
        <div class="text-center mb-10">
            <h1>Two-factor authentication</h1>
            <p>Manage two-factor authentication.</p>
        </div>
        
        <Box title="Two-factor authentication">
            <BoxContent>
                <p class="mb-4">Two-factor authentication is an extra layer of security for your Mintpad account in addition to the password. This extra layer prevents unwanted people from gaining access to your account when they have retrieved the password.</p>
                <h3>This is how it works</h3>
                <p class="mb-4">Every time you log in, you will be asked for an extra verification code in addition to your password. Before you activate two-step verification, you will receive 8 codes from us as a backup. Keep it in a safe place that only you can access. You can use these codes when you do not have access to the control code.</p>
                <p v-if="$page.props.flash.status == 'two-factor-authentication-confirmed'" class="mb-4">Two factor authentication confirmed and enabled successfully.</p>
                
                <div v-if="$page.props.flash.status == 'two-factor-authentication-enabled'" class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                    <div class="col-span-1 sm:col-span-2">
                        <h2>Step 1</h2>
                    </div>
                    <div class="mb-4">
                        <p class="mb-4">You have now enabled 2fa, please scan the following QR code into your phones authenticator application.</p>
                        <p class="text-center" v-html="user.twoFactorQrCodeSvg"></p>
                    </div>
                    <div class="mb-4">
                        <p class="mb-4">Please store these recovery codes in a secure location.</p>
                        <pre class="text-justify" v-html="user.recoveryCodes.join('<br/>')"></pre>
                    </div>
                    <div class="col-span-1 sm:col-span-2">
                        <h2>Step 2</h2>
                        <form @submit.prevent="confirm">
                            <Label class="w-full" for="code" value="Code" />
                            <Input id="code" class="w-full sm:w-1/5" type="text" v-model="formConfirm.code" required /><br/>
                            <Button class="w-full sm:w-1/5">Comfirm</Button>
                        </form>
                    </div>
                </div>
                <div v-else>
                    <form @submit.prevent="toggle">
                        <Button>{{  user.two_factor_secret ? 'Disable' : 'Enable' }}</Button>
                    </form>
                </div>
            </BoxContent>
        </Box>
    </AuthenticatedLayout>
</template>