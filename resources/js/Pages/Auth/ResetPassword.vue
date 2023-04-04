<script setup>
import AuthCard from '@/Components/AuthCard.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import Label from '@/Components/Form/Label.vue'
import ValidationMessage from '@/Components/Form/ValidationMessage.vue'
import ShowPassword from '@/Components/ShowPassword.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation')
    });
};
</script>
<template>
    <GuestLayout>
        <Head title="Forgot password" />
        <AuthCard>
            <form @submit.prevent="submit">
                <div class="relative">
                    <Label for="email" value="Email" />
                    <Input id="email" type="email" v-model="form.email" required autofocus autocomplete="username" />
                    <ValidationMessage :validation="form.errors.email" />
                </div>

                <div class="relative">
                    <Label for="password" value="Password" />
                    <Input id="password" type="password" v-model="form.password" required autocomplete="new-password" />
                    <ValidationMessage :validation="form.errors.password" />
                </div>

                <div class="relative">
                    <Label for="password_confirmation" value="Confirm password" />
                    <ShowPassword v-slot="slotProps">
                        <Input id="password_confirmation" :type="slotProps.type" v-model="form.password_confirmation" required autocomplete="new-password" />
                    </ShowPassword>
                </div>

                <div class="mt-4">
                    <Button class="w-full">Email Password Reset Link</Button>
                </div>
            </form>
        </AuthCard>
    </GuestLayout>
</template>