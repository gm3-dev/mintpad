<script setup>
import AuthCard from '@/Components/AuthCard.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import Label from '@/Components/Form/Label.vue'
import ShowPassword from '@/Components/ShowPassword.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'));
};
</script>
<template>
    <GuestLayout>
        <Head title="Confirm password" />
        <AuthCard>
            <div class="mb-4 text-sm text-mintpad-300">This is a secure area of the application. Please confirm your password before continuing.</div>

            <form @submit.prevent="submit">
                <div class="relative">
                    <Label for="password" value="Password" />
                    <ShowPassword v-slot="slotProps">
                        <Input id="password" :type="slotProps.type" v-model="form.password" required autocomplete="current-password" />
                    </ShowPassword>
                </div>

                <div class="mt-4">
                    <Button class="w-full">Confirm</Button>
                </div>
            </form>
        </AuthCard>
    </GuestLayout>
</template>