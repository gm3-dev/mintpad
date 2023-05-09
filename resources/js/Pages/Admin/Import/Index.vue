<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { onMounted, provide, ref } from 'vue'
import Box from '@/Components/Box.vue'
import BoxContent from '@/Components/BoxContent.vue'
import BoxRow from '@/Components/BoxRow.vue'
import Label from '@/Components/Form/Label.vue'
import Select from '@/Components/Form/Select.vue'
import Button from '@/Components/Form/Button.vue'
import InputFile from '@/Components/Form/InputFile.vue'
import { getBlockchains } from '@/Helpers/Blockchain'
import { getSelectInputBlockchainObject } from '@/Helpers/Helpers'
import { format } from 'date-fns'

const props = defineProps({
    'imports': Object
})

let blockchains = ref(getBlockchains())
let blockchainList = ref({})
let loading = ref(true)
let wallet = ref({account: false})
let validBlockchain = ref(true)

// Set months and years
let months = {
    1: 'January',
    2: 'February',
    3: 'March',
    4: 'April',
    5: 'May',
    6: 'June',
    7: 'July',
    8: 'August',
    9: 'September',
    10: 'October',
    11: 'November',
    12: 'December',
}
let years = {2022: 2022, 2023: 2023, 2024: 2024, 2025: 2025, 2026: 2026, 2027: 2027, 2028: 2028, 2029: 2029}

provide('wallet', wallet)
provide('transaction', {show: false, message: ''})

onMounted(async () => {
    blockchainList.value = getSelectInputBlockchainObject(blockchains)

    // Done loading
    loading.value = false
})

const formCsv = useForm({
    chain_id: 1,
    month: format(new Date(), 'M'),
    year: format(new Date(), 'Y'),
    file: null
})

const formInvoices = useForm({
    month: format(new Date(), 'M'),
    year: format(new Date(), 'Y')
})

const importCsv = () => {
    formCsv.post(route('admin.import.store'))
}
const importInvoices = () => {
    formInvoices.post(route('admin.invoices.store'))
}
</script>
<template>
    <AuthenticatedLayout :loading="loading" :valid-blockchain="validBlockchain">
        <Head title="Import" />

        <div class="text-center mb-10">
            <h1>Import</h1>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
            <Box title="New EVM-token import">
                <BoxContent>
                    <form @submit.prevent="importCsv" enctype="multipart/form-data">
                        <InputFile v-model="formCsv.file" />
                        
                        <div class="grid grid-cols-2 gap-x-4 mb-4">
                            <div>
                                <Label for="import-month" value="Month" class="w-full" />
                                <Select id="import-month" class="!w-full" v-model="formCsv.month" :options="months"></Select>
                            </div>
                            <div>
                                <Label for="import-year" value="Year" class="w-full" />
                                <Select id="import-year" class="!w-full" v-model="formCsv.year" :options="years"></Select>
                            </div>
                            <div class="col-span-2">
                                <Label for="import-chain_id" value="Blockchain" class="text-left ml-1 w-full" />
                                <Select id="import-chain_id" class="!w-full" v-model="formCsv.chain_id" :options="blockchainList"></Select>
                            </div>
                        </div>

                        <Button>Import</Button>
                    </form>

                </BoxContent>
            </Box>

            <Box title="Generate invoices manually">
                <BoxContent>
                    <p class="mb-4 text-sm">Normally you don't need to do this manually. A Cronjob will take care of this.</p>
                    <form @submit.prevent="importInvoices" enctype="multipart/form-data">                        
                        <div class="grid grid-cols-2 gap-x-4 mb-4">
                            <div>
                                <Label for="import-month" value="Month" class="w-full" />
                                <Select id="import-month" class="!w-full" v-model="formInvoices.month" :options="months"></Select>
                            </div>
                            <div>
                                <Label for="import-year" value="Year" class="w-full" />
                                <Select id="import-year" class="!w-full" v-model="formInvoices.year" :options="years"></Select>
                            </div>
                        </div>

                        <Button>Add to queue</Button>
                    </form>
                </BoxContent>
            </Box>

            <Box title="Import history">
                <BoxRow class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                    <div class="basis-2/5 text-mintpad-300 text-sm px-1">Blockchain</div>
                    <div class="basis-1/5 text-mintpad-300 text-sm">No. txn</div>
                    <div class="basis-1/5 text-mintpad-300 text-sm">Total</div>
                    <div class="basis-1/5 text-mintpad-300 text-sm px-1 text-right">Period</div>
                </BoxRow>
                <BoxRow v-for="importData in imports" class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5 p-1">{{ blockchains[importData.chain_id].name }} ({{ importData.chain_id }})</div>
                    <div class="basis-1/5 p-1">{{ importData.transaction_count }}</div>
                    <div class="basis-1/5 p-1">${{ importData.total }}</div>
                    <div class="basis-1/5 p-1 text-right">{{ importData.period }}</div>
                </BoxRow>
            </Box>
        </div>
    </AuthenticatedLayout>
</template>