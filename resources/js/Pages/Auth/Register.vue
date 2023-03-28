<script setup>
import AuthCard from '@/Components/AuthCard.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import Label from '@/Components/Form/Label.vue'
import Radio from '@/Components/Form/Radio.vue'
import RadioGroup from '@/Components/Form/RadioGroup.vue'
import Select from '@/Components/Form/Select.vue'
import Hyperlink from '@/Components/Hyperlink.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import _ from 'lodash'
import { format } from 'date-fns'
import { ref } from 'vue'
import Checkbox from '@/Components/Form/Checkbox.vue'
import ShowPassword from '@/Components/ShowPassword.vue'
import Messages from '@/Components/Messages.vue'
import ValidationMessage from '@/Components/Form/ValidationMessage.vue'

const props = defineProps({
    countries: Object,
    affiliate: String
})

let messages = ref([])
let signUpStep = ref(1)
let formIsValid = ref(true)
let form = useForm({
    name: '',
    birth_day: 1,
    birth_month: 1,
    birth_year: parseInt(format(new Date(),'Y')) - 1,
    reference: '',
    is_company: '0',
    company_name: '',
    vat_id: '',
    country: 'US',
    address: '',
    city: '',
    postalcode: '',
    state: '',
    email: '',
    password: '',
    password_confirmation: '',
    accept_tos: false,
    affiliate: props.affiliate
})
let errorClasses = ref('!border-red-500')
let validation = ref({
    name: false,
    reference: false,
    is_company: false,
    company_name: false,
    vat_id: false,
    country: false,
    address: false,
    city: false,
    postalcode: false,
    state: false,
    email: false,
    password: false,
    password_confirmation: false,
    accept_tos: false,
})

const nextSignUpStep = () => {
    validateForm(signUpStep.value)
                
    if (!formIsValid.value) {
        return
    }

    if (signUpStep.value == 4) {
        postForm()
    } else if (signUpStep.value == 1 && form.is_company == '1') {
        signUpStep.value = 2
    } else if (signUpStep.value == 1 && form.is_company == '0') {
        signUpStep.value = 3
    } else {
        signUpStep.value += 1
    }
}

const postForm = () => {
    form.post(route('register'), {
        onFinish: () => {

        },
        onError: (error) => {
            for (var key in validation.value) {
                if (error[key]) {
                    validation.value[key] = error[key]
                } else {
                    validation.value[key] = false
                }
            }
        }
    })
    // axios.post(route('register'), form).then((response) => {
    //     window.location.href = '/collections'
    // }).catch(error => {
    //     this.setValidationData()

    //     for (var field in error.response.data.errors) {
    //         this.validation[field] = error.response.data.errors[field][0]
    //     }
    // })
}

const validateForm = (step) => {
    formIsValid.value = true

    switch (step) {
        case 1:
            isRequired('name')
            isRequired('reference')
            break;
        case 2:
            isRequired('company_name')
            isRequired('vat_id')
            break;
        case 3:
            isRequired('country')
            isRequired('address')
            isRequired('city')
            isRequired('postalcode')
            isRequired('state')
            break;
    }
}
const isRequired = (name) => {
    if (form[name].trim() == '') {
        validation.value[name] = 'Field is required'
        formIsValid.value = false
    } else {
        validation.value[name] = false
    }
}
</script>
<template>
    <GuestLayout>
        <Head title="Sign in" />
        <AuthCard>
            <h1>Sign Up</h1>

            <form id="registration-form" method="POST" action="{{ route('register') }}">
                <div class="flex flex-row gap-2 mb-4">
                    <h3 class="basis-1/2 border-b-2 border-primary-200 pb-1 mb-1" :class="{'border-primary-600': signUpStep <= 3}">Personal Info</h3>
                    <h3 class="basis-1/2 border-b-2 border-primary-200 pb-1 mb-1" :class="{'border-primary-600': signUpStep > 3}">Account Details</h3>
                </div>

                <div>
                    <div v-show="signUpStep == 1">
                        <!-- Full name -->
                        <div class="relative">
                            <Label for="name" value="Full name" />
                            <Input id="name" type="text" v-model="form.name" :class="[validation.name ? errorClasses : '']" required autofocus />
                            <ValidationMessage :validation="validation.name" />
                        </div>
                        <!-- Date of birth -->
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
                        <!-- Reference -->
                        <div class="relative">
                            <Label for="reference" value="How did you hear about us?" />
                            <Input id="reference" class="w-full" type="text" v-model="form.reference" :class="[validation.reference ? errorClasses : '']" required />
                            <ValidationMessage :validation="validation.reference" />
                        </div>
                        <!-- Company -->
                        <div>
                            <Label value="Are you a company?" />
                            <RadioGroup>
                                <Radio id="is-company-no" type="radio" v-model="form.is_company" value="0" class="inline-block" /><Label for="is-company-no" class="inline-block mr-2" value="No" />
                                <Radio id="is-company-yes" type="radio" v-model="form.is_company" value="1" class="inline-block" /><Label for="is-company-yes" class="inline-block" value="Yes" /> 
                            </RadioGroup>      
                        </div>
                    </div>
                    <div v-show="signUpStep == 2">
                        <!-- Company name -->
                        <div class="relative">
                            <Label for="company_name" value="Company name" />
                            <Input id="company_name" class="w-full" type="text" v-model="form.company_name" :class="[validation.company_name ? errorClasses : '']" required />
                            <ValidationMessage :validation="validation.company_name" />
                        </div>
                        <!-- VAT ID -->
                        <div class="relative">
                            <Label for="vat_id" value="VAT ID number" />
                            <Input id="vat_id" class="w-full" type="text" v-model="form.vat_id" :class="[validation.vat_id ? errorClasses : '']" required />
                            <ValidationMessage :validation="validation.vat_id" />
                        </div>
                    </div>
                    <div v-show="signUpStep == 3">
                        <!-- Country -->
                        <div>
                            <Label for="country" value="Country" />
                            <Select id="country" class="!w-full" v-model="form.country" :options="countries"></Select>
                        </div>
                        <!-- Street address -->
                        <div class="relative">
                            <Label for="address" value="Street address" />
                            <Input id="address" type="text" v-model="form.address" :class="[validation.address ? errorClasses : '']" required />
                            <ValidationMessage :validation="validation.address" />
                        </div>
                        <!-- City -->
                        <div class="relative">
                            <Label for="city" value="City" />
                            <Input id="city" type="text" v-model="form.city" :class="[validation.city ? errorClasses : '']" required />
                            <ValidationMessage :validation="validation.city" />
                        </div>
                        <div class="flex flex-row gap-2">
                            <!-- Postal code -->
                            <div class="basis-1/2 relative">
                                <Label for="postalcode" value="Postal code" />
                                <Input id="postalcode" class="w-full" type="text" v-model="form.postalcode" :class="[validation.postalcode ? errorClasses : '']" required />
                                <ValidationMessage :validation="validation.postalcode" />
                            </div>
                            <!-- State/Province -->
                            <div class="basis-1/2 relative">
                                <Label for="state" value="State/Province" />
                                <Input id="state" class="w-full" type="text" v-model="form.state" :class="[validation.state ? errorClasses : '']" required />
                                <ValidationMessage :validation="validation.state" />
                            </div>
                        </div>
                    </div>
                    <div v-show="signUpStep == 4">
                        <!-- Email Address -->
                        <div class="relative">
                            <Label for="email" value="Email Address" />
                            <Input id="email" type="email" v-model="form.email" :class="[validation.email ? errorClasses : '']" required />
                            <ValidationMessage :validation="validation.email" />
                        </div>
                        <!-- Password -->
                        <div class="relative">
                            <Label for="password" value="Password" />
                            <ShowPassword v-slot="slotProps">
                                <Input id="password" :type="slotProps.type" v-model="form.password" :class="[validation.password ? errorClasses : '']" required autocomplete="new-password"/>
                            </ShowPassword>
                            <ValidationMessage :validation="validation.password" />
                        </div>
                        <!-- Confirm Password -->
                        <div class="relative">
                            <Label for="password_confirmation" value="Confirm Password" />
                            <ShowPassword v-slot="slotProps">
                                <Input id="password_confirmation" class="mb-0" :type="slotProps.type" v-model="form.password_confirmation" :class="[validation.password_confirmation ? errorClasses : '']" required autocomplete="confirm-password"/>
                            </ShowPassword>
                            <ValidationMessage :validation="validation.password_confirmation" />
                        </div>

                        <label for="accept-tos" class="relative inline-flex items-center flex-auto mt-4">
                            <Checkbox id="accept-tos" class="align-top" type="checkbox" value="1" v-model="form.accept_tos" />
                            <Label for="accept-tos" class="ml-2">I agree to the <Hyperlink href="https://mintpad.co/terms-of-service/" target="_blank" class="text-xs">Terms of Service</Hyperlink> and the <Hyperlink href="https://mintpad.co/privacy-policy/" target="_blank" class="text-xs">Privacy Policy</Hyperlink> from Mintpad.</Label>
                            <ValidationMessage :validation="validation.accept_tos" class="-bottom-[11px]"/>
                        </label>
                    </div>
                </div>

                <Button  @click.prevent="nextSignUpStep" class="w-full mt-4">{{ signUpStep == 4 ? 'Submit' : 'Next' }}</Button>

                <p class="text-xs text-center mt-4">Already have an account? <Hyperlink :href="route('login')">Sign In</Hyperlink></p>

            </form>
        </AuthCard>

        <Messages :messages="messages"/>
    </GuestLayout>
</template>