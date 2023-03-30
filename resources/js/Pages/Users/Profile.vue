<script setup>
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import Button from '@/Components/Form/Button.vue'
import Checkbox from '@/Components/Form/Checkbox.vue'
import Input from '@/Components/Form/Input.vue'
import Label from '@/Components/Form/Label.vue'
import Select from '@/Components/Form/Select.vue'
import Hyperlink from '@/Components/Hyperlink.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, provide, onMounted } from 'vue'
import _ from 'lodash'
import { format } from 'date-fns'
import { connectWallet } from '@/Wallets/Wallet'

const props = defineProps({
    user: Object,
    countries: Object
})

let loading = ref(true)
let wallet = ref(false)
let validBlockchain = ref(true)

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    country: props.user.country,
    city: props.user.city,
    state: props.user.state,
    postalcode: props.user.postalcode,
    address: props.user.address,
    birth_month: props.user.birth_month,
    birth_day: props.user.birth_day,
    birth_year: props.user.birth_year,
    is_company: Boolean(props.user.is_company),
    company_name: props.user.company_name,
    vat_id: props.user.vat_id,
    affiliate_code: props.user.affiliate_code,
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
                        <!-- Country -->
                        <div>
                            <Label for="country" value="Country" class="relative is-required" />
                            <Select id="country" class="block !w-full" v-model="form.country" :options="countries"></Select>
                        </div>
                        <!-- City -->
                        <div>
                            <Label for="city" value="City" class="relative is-required" />
                            <Input id="city" type="text" v-model="form.city" required />
                        </div>
                        <!-- State/Province -->
                        <div>
                            <Label for="state" value="State/Province" class="relative is-required" />
                            <Input id="state" type="text" v-model="form.state" required />
                        </div>
                        <!-- Postal code -->
                        <div>
                            <Label for="postalcode" value="Postal code" class="relative is-required" />
                            <Input id="postalcode" type="text" v-model="form.postalcode" required />
                        </div>
                        <!-- Streetaddress -->
                        <div>
                            <Label for="address" value="Street address" class="relative is-required" />
                            <Input id="address" type="text" v-model="form.address" required />
                        </div>
                        <!-- Date of birth -->
                        <div>
                            <Label for="name" value="Date of birth" />
                            <div class="flex flex-row gap-2">
                                <div class="basis-1/3">
                                    <Select id="birth-month" class="!w-full" v-model="form.birth_month" :options="_.zipObject(_.range(1,13),_.range(1,13))"></Select>
                                </div>
                                <div class="basis-1/3">
                                    <Select id="birth-day" class="!w-full" v-model="form.birth_day" :options="_.zipObject(_.range(1,32),_.range(1,32))"></Select>
                                </div>
                                <div class="basis-1/3">
                                    <Select id="birth-year" class="!w-full" v-model="form.birth_year" :options="_.zipObject(_.range(1920,format(new Date(),'Y')),_.range(1920,format(new Date(),'Y')))"></Select>
                                </div>
                            </div>
                        </div>
                    </div>
                </BoxContent>
            </Box>

            <Box title="Company information">
                <BoxContent>
                    <!-- Is company -->
                    <div class="mb-4">
                        <label for="is-company" class="w-1/2">
                            <Checkbox id="is-company" type="checkbox" value="1" v-model:checked="form.is_company" />
                            <Label for="is-company" class="inline ml-2" value="I'm a company" />
                        </label>
                    </div>

                    <div id="company-info" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Company name -->
                        <div>
                            <Label for="company_name" value="Company name" class="relative is-required" />
                            <Input id="company_name" type="text" v-model="form.company_name" />
                        </div>
                        <!-- VAT ID -->
                        <div>
                            <Label for="vat_id" value="VAT ID number" class="relative is-required" />
                            <Input id="vat_id" type="text" v-model="form.vat_id" />
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