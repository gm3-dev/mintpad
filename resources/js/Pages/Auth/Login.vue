<script setup>
import AuthCard from '@/Components/AuthCard.vue'
import Hyperlink from '@/Components/Hyperlink.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import Label from '@/Components/Form/Label.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import ShowPassword from '@/Components/ShowPassword.vue'
import ValidationMessage from '@/Components/Form/ValidationMessage.vue'

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

// Method to open the forgot password page in a new tab
const openForgotPassword = () => {
    window.open("https://password.mintpad.co/forgot-password", "_blank");
};
</script>

<template>
    <GuestLayout>
        <Head title="Sign in" />
        <AuthCard>
            <h1>Sign In</h1>

            <form @submit.prevent="submit">
                <div class="relative">
                    <Label for="email" value="Email" />
                    <Input id="email" type="email" v-model="form.email" required autofocus autocomplete="username" />
                    <ValidationMessage :validation="form.errors.email" />
                </div>

                <div class="relative">
                    <Label for="password" value="Password" />
                    <ShowPassword v-slot="slotProps">
                        <Input id="password" :type="slotProps.type" v-model="form.password" required autocomplete="current-password" />
                    </ShowPassword>
                    <ValidationMessage :validation="form.errors.password" />
                </div>

                <div class="my-5 flex">
                    <Hyperlink 
                        class="text-xs" 
                        @click="openForgotPassword" 
                        style="cursor: pointer;" <!-- Optional: change cursor to pointer -->
                    >
                        Forgot your password?
                    </Hyperlink>
                </div>

                <Button class="w-full">Sign in</Button>

                <div>
                    <p class="text-xs text-center mt-4">Don’t have an account? <Hyperlink :href="route('register')">Sign up</Hyperlink></p>
                </div>
            </form>
        </AuthCard>
    </GuestLayout>
</template>
