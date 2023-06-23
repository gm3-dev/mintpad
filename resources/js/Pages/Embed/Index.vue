<script setup>
import $ from 'jquery'
import MinimalLayout from '@/Layouts/MinimalLayout.vue'
import { ref, onMounted, watch, computed, inject } from 'vue'
import axios from 'axios'
import { getDummyCollection, setStyling } from '@/Helpers/Helpers'
import EditorBar from '@/Pages/Embed/Partials/EditorBar.vue'
import EmbedContent from '@/Pages/Embed/Partials/EmbedContent.vue'
import Box from '@/Components/Box.vue'
import Modal from '@/Components/Modal.vue'
import BoxContent from '@/Components/BoxContent.vue'
import ButtonDefault from '@/Components/Form/ButtonDefault.vue'
import Checkbox from '@/Components/Form/Checkbox.vue'
import Label from '@/Components/Form/Label.vue'
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content
}

const props = defineProps({
    collection: Object
})

const data = getDummyCollection()
let editMode = ref(true)
let settingsChanged = ref(false)
let showModal = ref(false)
let collectionData = ref({
    loading: false,
    id: props.collection.id,
    theme: {
        primary: {r: 0, g: 119, b: 255, a: 1},
        background: {r: 255, g: 255, b: 255, a: 1},
        phases: {r: 241, g: 243, b: 244, a: 1}
    },
    settings: {
        phases: true,
        darkmode: false
    },
    claimPhases: data.claimPhases,
    timers: data.timers,
    activeMintPhase: 1,
    totalSupply: data.collection.totalSupply,
    totalClaimedSupply: data.collection.totalClaimedSupply,
    totalRatioSupply: data.collection.totalRatioSupply,
    nfts: data.collection.nfts
})
let loading = ref(true)
let validBlockchain = ref(true)
let embedUrl = ref('')
const emitter = inject('emitter')

onMounted(() => {
    axios.get('/collection/'+props.collection.id+'/fetch').then((response) => {
        // Set theme for mint
        if (response.data.theme.embed) {
            collectionData.value.theme = response.data.theme.embed
        }

        // Set settings
        if (response.data.settings.embed) {
            collectionData.value.settings = response.data.settings.embed
        }

        setStyling(collectionData.value)

        embedUrl.value = response.data.embed_url

        // Done loading
        loading.value = false
    })
})

const phasesSetting = computed(() => {
    return collectionData.value.settings.phases
})
watch(phasesSetting, (newValue, oldValue) => {
    if (oldValue != null) {
        settingsChanged.value = true
    }
})
emitter.on('settings-updated', (data) => {
    settingsChanged.value = false
})

const copyEmbedCode = (e) => {
    const height = collectionData.value.settings.phases ? '369px' : '233px'
    const iframe = '<iframe frameborder="0" width="600px" height="'+height+'" src="'+embedUrl.value+'"></iframe>';

    var target = $(e.target)
    var text = target.text()
    target.text('Copied')
    setTimeout(function() {
        target.text(text)
    }, 1000)
    navigator.clipboard.writeText(iframe)
}
</script>
<template>
    <MinimalLayout :loading="loading" :overlay="loading" :valid-blockchain="validBlockchain" :chain-id="collection.chain_id">
        <EditorBar :edit-mode="editMode" :collection-data="collectionData" />

        <div class="max-w-7xl mx-auto px-6 pt-28">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4">
                <EmbedContent :edit-mode="editMode" :collection="collection" :collection-data="collectionData" />
                <Box class="mb-0" title="Settings">
                    <BoxContent>
                        <div class="mb-2">
                            <Checkbox id="settings-phases" class="align-middle" type="checkbox" value="1" v-model:checked="collectionData.settings.phases" />
                            <Label for="settings-phases" class="ml-2 mt-1">Show mint phases</Label>
                        </div>
                        <div class="mb-4">
                            <Checkbox id="settings-darkmode" class="align-middle" type="checkbox" value="1" v-model:checked="collectionData.settings.darkmode" />
                            <Label for="settings-darkmode" class="ml-2 mt-1">Darkmode</Label>
                        </div>
                        <div>
                            <ButtonDefault v-if="!settingsChanged" @click.prevent="copyEmbedCode" class="w-48">Copy embed code</ButtonDefault>
                            <span v-else class="inline-block ml-2" content="Publish your changes first" v-tippy="{ arrow : true }">
                                <ButtonDefault disabled="disabled" class="w-48">Copy embed code</ButtonDefault>
                            </span>
                        </div>
                    </BoxContent>
                </Box>
            </div>
        </div>

        <div v-if="editMode" class="fixed left-0 bottom-0 p-2 w-full bg-primary-600 text-white">
            <div class="max-w-3xl lg:max-w-5xl mx-auto px-6 lg:px-0">
                <p class="!text-white text-center font-medium text-sm !mb-0">We use demo data for showcase purposes</p>
            </div>
        </div>

        <Modal :show="showModal" title="Mint successful!" @close="showModal = false">
            <p>You have an NFT in your wallet! You can now trade this NFT on OpenSea and other marketplaces.</p>
            <p class="!text-primary-600 mint-text-primary">Good luck with trading!</p>
        </Modal>
    </MinimalLayout>
</template>