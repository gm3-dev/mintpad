<script setup>
import MinimalLayout from '@/Layouts/MinimalLayout.vue'
import Logo from '@/Components/Logo.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    status: Number
})

const setTitle = () => {
    switch(props.status) {
        case 401: return 'Unauthorized'
        case 403: return 'Forbidden'
        case 404: return 'Page not found'
        case 419: return 'Page Expired'
        case 429: return 'Too Many Requests'
        case 500: return 'Something went wrong'
        case 503: return 'Service Unavailable'
        default: return 'Oops'
    }
}
const setDescription = () => {
    switch(props.status) {
        case 401: return 'You should not be here'
        case 403: return 'You should not be here'
        case 404: return 'Are you sure this is the correct URL?'
        case 419: return 'Please try again'
        case 429: return 'Please try again later'
        case 500: return 'Please try again, or contact support'
        case 503: return 'Please try again later'
        default: return 'Something went wrong'
    }
}

let title = ref(setTitle())
let description = ref(setDescription())
</script>
<template>
    <MinimalLayout>
        <Head :title="title" />
        
        <div class="overflow-hidden text-center pt-12">
            <Logo />

            <div>
                <span class="inline-block w-full text-primary-300 mt-12 mb-6 text-9xl text-center">{{ status }}</span>
                <div class="text-center">
                    <h2 class="text-3xl text-center mb-1 font-semibold">{{ title }}</h2>
                    <p class="text-center text-lg">{{ description }}</p>
                </div>        
            </div>
        </div>
    </MinimalLayout>
</template>