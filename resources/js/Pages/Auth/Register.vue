<script setup>
import AuthCard from '@/Components/AuthCard.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import Label from '@/Components/Form/Label.vue'
import Hyperlink from '@/Components/Hyperlink.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import _ from 'lodash'
import { ref } from 'vue'
import Checkbox from '@/Components/Form/Checkbox.vue'
import ShowPassword from '@/Components/ShowPassword.vue'
import Messages from '@/Components/Messages.vue'
import ValidationMessage from '@/Components/Form/ValidationMessage.vue'

const props = defineProps({
    affiliate: String
})

let messages = ref([])
let form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    accept_tos: false,
    affiliate: props.affiliate
})
let errorClasses = ref('!border-red-500')
let validation = ref({
    name: false,
    email: false,
    password: false,
    password_confirmation: false,
    accept_tos: false,
})

const submitForm = () => {
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
</script>
<template>
    <GuestLayout>
        <Head title="Sign in" />
        <AuthCard>
            <h1>Sign Up</h1>

            <form id="registration-form" method="POST" action="{{ route('register') }}">
                <div>
                    <!-- Name -->
                    <div class="relative">
                        <Label for="name" value="Name" />
                        <Input id="name" type="text" v-model="form.name" :class="[validation.name ? errorClasses : '']" required autofocus />
                        <ValidationMessage :validation="validation.name" />
                    </div>
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

                    <label for="accept-tos" class="relative inline-flex items-start flex-auto mt-4">
                        <Checkbox id="accept-tos" class="align-top" type="checkbox" value="1" v-model="form.accept_tos" />
                        <Label for="accept-tos" class="ml-2">I agree to the <Hyperlink href="https://mintpad.co/terms-of-service/" target="_blank" class="text-xs">Terms of Service</Hyperlink> and the <Hyperlink href="https://mintpad.co/privacy-policy/" target="_blank" class="text-xs">Privacy Policy</Hyperlink> from Mintpad.</Label>
                        <ValidationMessage :validation="validation.accept_tos" class="!-bottom-[11px]"/>
                    </label>
                </div>

                <Button @click.prevent="submitForm" class="w-full mt-4">Submit</Button>

                <p class="text-xs text-center mt-4">Already have an account? <Hyperlink :href="route('login')">Sign In</Hyperlink></p>

            </form>
        </AuthCard>

        <Messages :messages="messages"/>
    </GuestLayout>
</template>