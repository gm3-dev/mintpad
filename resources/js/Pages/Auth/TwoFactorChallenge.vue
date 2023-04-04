<script setup>
import AuthCard from '@/Components/AuthCard.vue'
import Button from '@/Components/Form/Button.vue'
import Input from '@/Components/Form/Input.vue'
import Label from '@/Components/Form/Label.vue'
import ValidationMessage from '@/Components/Form/ValidationMessage.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
    code: '',
})

const submit = () => {
    form.post('/two-factor-challenge')
};
</script>
<template>
    <GuestLayout>
        <Head title="2FA challenge" />
        <AuthCard>
            <div class="mb-4 text-sm text-mintpad-300">Enter your authentication code to login.</div>

            <form @submit.prevent="submit">
                <div class="relative">
                    <Label for="code" value="Password" />
                    <Input id="code" type="text" v-model="form.code" required />
                    <ValidationMessage :validation="form.errors.code" />
                </div>

                <div class="mt-4">
                    <Button class="w-full">Confirm</Button>
                </div>
            </form>
        </AuthCard>
    </GuestLayout>
</template>