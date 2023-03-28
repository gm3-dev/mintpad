<script setup>
import Box from '@/Components/Box.vue'
import BoxRow from '@/Components/BoxRow.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import LinkBlue from '@/Components/LinkBlue.vue'
import { Head } from '@inertiajs/vue3'
import { ref, provide, onMounted } from 'vue'

defineProps({
    'users': Object,
})

let loading = ref(true)
let wallet = ref({account: false})
let validBlockchain = ref(true)

provide('wallet', wallet)

onMounted(async () => {
    // Done loading
    loading.value = false
})
</script>
<template>
    <AuthenticatedLayout :loading="loading" :valid-blockchain="validBlockchain">
        <Head title="Users" />

        <div class="text-center mb-10">
            <h1>Users</h1>
        </div>
        
        <Box title="User list">
            <BoxRow class="flex flex-row text-sm dark:text-mintpad-300 font-jpegdevmd">
                <div class="basis-1/6">Name</div>
                <div class="basis-1/6">Role</div>
                <div class="basis-1/6">Collections</div>
                <div class="basis-1/6">status</div>
                <div class="basis-2/6"></div>
            </BoxRow>
            <BoxRow v-for="user in users" class="flex flex-row text-sm items-center text-mintpad-700 dark:text-white font-medium">
                <div class="basis-1/6">{{ user.name }}</div>
                <div class="basis-1/6">{{ user.role }}</div>
                <div class="basis-1/6">
                    {{ user.collection_count }} collection(s)
                    <span v-if="user.collection_count" class="inline-block ml-2" :content="user.collection_list" v-tippy="{ arrow : true }">
                        <i class="fas fa-question-circle text-sm text-mintpad-700 dark:text-gray-200"></i>
                    </span>
                </div>
                <div class="basis-1/6">{{ user.status }}</div>
                <div class="basis-2/6 text-right">
                    <LinkBlue :href="route('admin.users.edit', user.id)" class="ml-2">Edit</LinkBlue>
                </div>
            </BoxRow>
        </Box>
        
    </AuthenticatedLayout>
</template>