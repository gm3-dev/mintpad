<script setup>
import Button from '@/Components/Form/Button.vue'
import ButtonDefault from '@/Components/Form/ButtonDefault.vue'
import ButtonGray from '@/Components/Form/ButtonGray.vue'
import Input from '@/Components/Form/Input.vue'
import LinkBlue from '@/Components/LinkBlue.vue'
import Modal from '@/Components/Modal.vue'
import { inject, ref } from 'vue'

const props = defineProps({
    editMode: Boolean,
    collectionData: Object
})
let editor = ref({
    index: -1,
    href: '',
    label: ''
})
let showModal = ref(false)
const emitter = inject('emitter')

const deleteButton = () => {
    props.collectionData.buttons.splice(editor.value.index, 1)
}

const editButton = (index) => {
    const button = props.collectionData.buttons[index]
    editor.value.label = button.label
    editor.value.href = button.href
    editor.value.index = index
    showModal.value = true
}

const newButton = () => {
    buttonEditor.value = {label: '', href: '', index: -1}
    showModal.value = true
}

const addNewButton = () => {
    if (editor.value.label == '' || editor.value.href == '') {
        emitter.emit('new-message', {type: 'error', message: 'Label and link are both required'})
    } else {
        if (editor.value.index == -1) {
            props.collectionData.buttons.push({label: editor.value.label, href: editor.value.href})
        } else {
            props.collectionData.buttons[editor.value.index] = {label: editor.value.label, href: editor.value.href}
        }
        showModal.value = false
    }
}
</script>
<template>
    <div v-if="editMode">
        <span class="inline-block" v-for="(button,index) in collectionData.buttons" content="Edit button" v-tippy>
            <Button @click.prevent="editButton(index)" :href="button.href" :target="'_blank'" class="m-2 mint-bg-primary">{{ button.label }} <i class="fas fa-edit"></i></Button>
        </span>
        <span class="inline-block" content="Add button" v-tippy>
            <ButtonDefault href="#" @click.prevent="newButton" class="!px-3"><i class="fa-solid fa-plus mr-4 text-lg align-middle"></i> <span class="align-middle">Add button</span></ButtonDefault>
        </span>
    </div>
    <div v-else>
        <LinkBlue element="a" v-for="button in collectionData.buttons" :href="button.href" :target="'_blank'" class="m-2 mint-bg-primary" rel="nofollow">{{ button.label }}</LinkBlue>
    </div>

    <Modal :show="showModal" title="Edit button" @close="showModal = false">
        <form>
            <div class="flex gap-2">
                <Input type="text" v-model="editor.label" placeholder="Label" />
                <Input type="text" v-model="editor.href" placeholder="Link" />
            </div>
            <div class="mt-4">
                <span class="inline-block" content="Delete button" v-tippy>
                    <ButtonGray href="#" class="!px-4" @click.prevent="deleteButton"><i class="fas fa-trash-alt"></i></ButtonGray>
                </span>
                <span class="float-right inline-block" content="Save" v-tippy>
                    <Button href="#" class="!px-4" @click.prevent="addNewButton">Save</Button>
                </span>
            </div>
        </form>
    </Modal>
</template>