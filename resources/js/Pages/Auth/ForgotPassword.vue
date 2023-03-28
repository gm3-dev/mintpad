<script setup>
import AuthCard from '@/Components/AuthCard.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import Label from '@/Components/Form/Label.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>
<template>
    <GuestLayout>
        <Head title="Forgot password" />
        <AuthCard>
            <div class="mb-4 text-sm text-mintpad-300">Enter your email address that you used to register. We'll send you an email with a link to reset your password.</div>

            <form @submit.prevent="submit">
                <div>
                    <Label for="email" value="Email" />
                    <Input id="email" type="email" v-model="form.email" required autofocus autocomplete="username" />
                </div>

                <div class="mt-4">
                    <Button class="w-full">Email Password Reset Link</Button>
                </div>
            </form>
        </AuthCard>
    </GuestLayout>
</template>